<?php

namespace App\Jobs;

use App\Datasource\NetworkHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class UpdateCache extends Job
{
    private $taskData;
    protected $currentService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $taskData)
    {
        $this->taskData = $taskData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // make request for each task

        try {
            foreach ($this->taskData as $task) {
                if (isset($task['delete'])) {
                    $deleteKeys = $task['delete'];

                    foreach ($deleteKeys as $key) {
                        $redisDataKeys = Redis::keys($key . '*');

                        foreach ($redisDataKeys as $dataKey) {
                            $prefixString = evn('APP_NAME') . '-database:';
                            $finalKey = substr($dataKey, strlen($prefixString));
                            Redis::delete($finalKey);
                        }
                    }
                } else {
                    $serviceName = "\App\Services\\" . $task['service'];

                    $this->currentService = new $serviceName(false);

                    $this->currentService->{$task['method']}(isset($task['param']) ? $task['param'] : null);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
