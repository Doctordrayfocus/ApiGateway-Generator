<?php


namespace App\Datasource;

use App\Jobs\UpdateCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class NetworkHandler
{
    protected $service_url;
    protected $httpClient;
    protected $defaultCache;

    public function __construct(String $service_url, $defaultCache = true, $headers = [])
    {
        $this->service_url = $service_url;
        $this->defaultCache = $defaultCache;
        $mainHeaders = [
           // "Authorization" => "Bearer " . request()->bearerToken()
        ];
        $this->httpClient = Http::withHeaders(array_merge($mainHeaders, $headers))
        ->timeout(100);
    }

    private function dataIsDifferent(array $oldData, array $newData)
    {
        if (!is_array($oldData) || !is_array($newData)) {
            return true;
        }

        if (count($oldData) != count($newData)) {
            return true;
        }

        foreach ($oldData as $key => $value) {
            if (isset($newData[$key])) {
                $dataValue = $value;

                if (is_array($dataValue)) {
                    if (is_array($newData[$key])) {
                        if ($this->dataIsDifferent($newData[$key], $value)) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                } elseif ($newData[$key] != $value) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    private function publishData($key, $data)
    {
        $message = json_encode($data);
        Redis::set($key, $message);
    }

    private function consumeData($key)
    {
        $data = Redis::get($key);

        if ($data) {
            return json_decode($data, true);
        } else {
            return null;
        }
    }

    private function handleResponse($response, $key = '', $cacheData = false, $requestData = [], $updateTasks = [])
    {
        if ($response->ok()) {
            if ($cacheData) {
                $this->publishData($key, $response->json());

                if (count($requestData) > 0) {
                    $this->publishData($key . '-request', $requestData);
                }
            }
            return $response->json();
        } else {
            return $response->throw();
        }
    }


    public function get(String $path, String $params = '', $cacheable = true)
    {
        $fullUrl = $this->service_url . $path . $params;

        if ($this->defaultCache && $cacheable) {
            $dataFromCache = $this->consumeData($fullUrl);

            if ($dataFromCache) {
                return $dataFromCache;
            }
        }

        $response = $this->httpClient->get($fullUrl);

        return $this->handleResponse($response, $fullUrl, $cacheable);
    }

    public function post(String $path, array $data, $cacheable = false, $updateTasks = [])
    {
        if (count($updateTasks) > 0) {
            $cacheUpdator = new UpdateCache($updateTasks);
            dispatch($cacheUpdator);
        }

        $fullUrl = $this->service_url . $path;

        if ($cacheable && $this->defaultCache) {
            $requestDataFromCache = $this->consumeData($fullUrl . 'post-request');
            $dataFromCache = $this->consumeData($fullUrl . 'post');

            if ($dataFromCache && $this->dataIsDifferent($requestDataFromCache, $data) == false) {
                return $dataFromCache;
            }
        }

        $response = $this->httpClient->post($fullUrl, $data);

        return $this->handleResponse($response, $fullUrl . 'post', $cacheable, $data, $updateTasks);
    }

    public function uploadFile(String $path, $file, $name)
    {
        $fullUrl = $this->service_url . $path;
        $response = $this->httpClient->attach('attachment', file_get_contents($file), $name)->post($fullUrl, []);

        return $this->handleResponse($response);
    }


    public function put(String $path, array $data, $updateTasks = [])
    {
        if (count($updateTasks) > 0) {
            $cacheUpdator = new UpdateCache($updateTasks);
            dispatch($cacheUpdator);
        }

        $fullUrl = $this->service_url . $path;

        $response = $this->httpClient->put($fullUrl, $data);

        return $this->handleResponse($response, '', false, [], $updateTasks);
    }

    public function delete(String $path, $updateTasks = [])
    {
        if (count($updateTasks) > 0) {
            $cacheUpdator = new UpdateCache($updateTasks);
            dispatch($cacheUpdator);
        }

        $fullUrl = $this->service_url . $path;

        $response = $this->httpClient->delete($fullUrl);

        return $this->handleResponse($response, '', false, [], $updateTasks);
    }
}
