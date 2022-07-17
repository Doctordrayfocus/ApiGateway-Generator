<?php

namespace App\Models\Users;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class User extends Model
{
    protected $connection = 'users';

    use ReadOnlyTrait;

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
