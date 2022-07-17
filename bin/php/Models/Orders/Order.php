<?php

namespace App\Models\Orders;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class Order extends Model
{
    protected $connection = 'orders';

    use ReadOnlyTrait;

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
