<?php

namespace App\Datasource\DBQueries;

use App\Models\User\User;

class DBUser
{
    public function getUserWithOrder($id)
    {
        return User::where('id', $id)->with('orders')->first();
    }
}
