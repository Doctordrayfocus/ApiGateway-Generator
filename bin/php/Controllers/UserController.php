<?php

namespace App\Http\Controllers;

use App\Datasource\DBQueries\DBUser;
use App\Jobs\AsyncRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use RespondsWithHttpStatus;

    protected $userService;
    protected $userDB;

    public function __construct(
        UserService $userService,
        DBUser $userDB
    ) {
        // for microservice network request
        $this->userService = $userService;
        // for complex database queries
        $this->userDB = $userDB;
    }


    /**
    *  @OA\Get(
    **  path="/api/users/{user_id}",
    *   tags={"USERS"},
    *   summary="Get user data",
    *     security={
    *            {"apiAuth":{}}
    *              },
    *
    *     @OA\Response(
    *                  response=200,
    *                  description="Success"
    *                 ),
    *     @OA\Response(
    *                  response=404,
    *                  description="Not Found"
    *                 ),
    *     @OA\Response(
    *                  response=500,
    *                  description="Internal Server Error"
    *                  )
    * )
    */

    public function getUser($user_id)
    {
        try {
            $user = $this->userService->getUser($user_id);
        } catch (\Exception $exception) {
            return $this->failure($exception->getMessage(), 500);
        }
        return $this->success('User data', $user, 200);
    }

    /**
    *  @OA\Get(
    **  path="/api/users/orders/{user_id}",
    *   tags={"USERS"},
    *   summary="Get user with orders",
    *     security={
    *            {"apiAuth":{}}
    *              },
    *
    *     @OA\Response(
    *                  response=200,
    *                  description="Success"
    *                 ),
    *     @OA\Response(
    *                  response=404,
    *                  description="Not Found"
    *                 ),
    *     @OA\Response(
    *                  response=500,
    *                  description="Internal Server Error"
    *                  )
    * )
    */

    public function getUserwithOrders($user_id)
    {
        try {
            $user = $this->userDB->getUserWithOrder($user_id);
        } catch (\Exception $exception) {
            return $this->failure($exception->getMessage(), 500);
        }
        return $this->success('User with orders', $user, 200);
    }


    /**
    *  @OA\Post(
    **  path="/api/users",
    *   tags={"USERS"},
    *   summary="Add new user",
    *     security={
    *            {"apiAuth":{}}
    *              },
    *
    *     @OA\Response(
    *                  response=200,
    *                  description="Success"
    *                 ),
    *     @OA\Response(
    *                  response=404,
    *                  description="Not Found"
    *                 ),
    *     @OA\Response(
    *                  response=500,
    *                  description="Internal Server Error"
    *                  )
    * )
    */

    public function addUser(Request $request)
    {
        try {

            // sync request example
            $responseData = $this->userDB->addUser($request);

            // async request example
            dispatch(new AsyncRequest([
                'param' => $request,
                'method' => 'addUser',
                'service'=> 'UserService'
            ]));
            $responseData = true;
        } catch (\Exception $exception) {
            return $this->failure($exception->getMessage(), 500);
        }
        return $this->success('User Added', $responseData, 200);
    }
}
