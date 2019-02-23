<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //只允许订单的创建者可以看到对应的订单信息
    public function own(User $user,Order $order)
    {
        return $user->id === $order->user_id;
    }
}
