<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order as OrderModel;

class Order extends Controller
{
    /**
     * 下单;用户端
     */
    public function createOrder()
    {
        // TODO
    }

    /**
     * 取消订单;用户端,师傅端
     */
    public function cancelOrder()
    {
        // TODO
    }

    /**
     * 撤销订单;用户端,师傅端
     */
    public function revokeOrder()
    {
        // TODO
    }

    /**
     * 更新订单; 用户端,师傅端
     */
    public function updateOrder()
    {
        // TODO
    }

    /**
     * 接单;师傅端
     */
    public function receiveOrder()
    {
        // TODO
    }

    /**
     * 派送订单;后台管理员
     */
    public function dispatchOrder()
    {
        // TODO
    }

    /**
     * 订单状态;用户端,师傅端
     * 待接单,已接单,未付款,已付款,已完成,已付款,已取消,已撤单
     */
    public function statusOrder()
    {
        // TODO
    }

    /**
     * 订单日志;用户端,师傅端
     * 订单记录,交易记录
     */
    public function logOrder()
    {
        // TODO
    }

}