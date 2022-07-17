<?php


namespace App\Services;

use App\Datasource\NetworkHandler;

class UserService
{
    protected $serviceUrl;
    protected $userNetwork;
    protected $serviceName = 'UserService';

    public function __construct($useCache = true, $headers = [])
    {
        $this->serviceUrl = 'svc.cluster.local:80/v1'; // this is your service's external or internal url
        $this->userNetwork = new NetworkHandler($this->serviceUrl, $useCache, $headers);
    }

    // get request sample using cache by default
    public function getUser($user_id)
    {
        return $this->userNetwork->get('/id' . '/' . $user_id);
    }

    // get request sample using network by default
    public function getUserFromNetwork($user_id)
    {
        return $this->userNetwork->get('/id' . '/' . $user_id, '', false);
    }

    // post request with cache update tasks
    public function addUser($request)
    {
        $updateTasks = [];
        array_push(
            $updateTasks,
            [
                'delete' => [$this->serviceUrl . '/users?'],

            ],
            [
                'param' => $request->user_id,
                'method' => 'getUser',
                'service'=> $this->serviceName
            ],
        );

        return $this->userNetwork->post('', $request->all(), false, $updateTasks);
    }


    // put request with cache update tasks
    public function updateUser($request)
    {
        $updateTasks = [];
        array_push(
            $updateTasks,
            [
                'param' => $request->user_id,
                'method' => 'getUser',
                'service'=> $this->serviceName
            ],
        );

        return $this->userNetwork->put('', $request->all(), false, $updateTasks);
    }

    // delete request with cache update tasks
    public function deleteUser($user_id)
    {
        $updateTasks = [];
        array_push(
            $updateTasks,
            [
                'delete' => [
                    $this->serviceUrl . '/users/id/' . $user_id,
                    $this->serviceUrl . '/users?'
                ],
            ]
        );
        return $this->userNetwork->delete('/' . $user_id, $updateTasks);
    }
}
