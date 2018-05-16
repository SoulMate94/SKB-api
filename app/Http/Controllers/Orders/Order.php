<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order as OrderModel;
use App\Traits\Tool;

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
    public function statusOrder(
        int $order_id = 0,
        int $status   = 1,
        int $page     = 1
    ){
        if (!in_array($status, [1,2,3,4])) {
            return Tool::jsonResp([
                'err' => 500,
                'msg' => '订单的状态不正常'
            ]);
        }

        $data = OrderModel::query()
                ->where([
                    'order_id'  => $order_id,
                    'is_closed' => 0
                ])
                ->where(function ($query) use ($status) {
                    switch ($status) {
                        case 1:
                            $query->where([
                                'order_status' => 1,
                                'pay_status'   => 1
                            ]);
                            break;

                        case 2:
                            $query->where('pay_status', 1);
                            $query->whereBetween('order_status', [1, 2, 3]);
                            break;

                        case 3:
                            $query->where([
                                'order_status' => 88,
                                'pay_status'   => 1,
                            ]);
                            break;

                        case 4:
                            $query->where([
                                'order_status' => -1,
                                'pay_status'   => 1
                            ]);
                            break;
                    }

                    $query->orderby('order_id', 'desc');
                })
                ->skip(($page - 1) * 20)
                ->take(20)
                ->get([
                    'order_id', 'order_status'
                ]);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => '获取订单列表成功',
            'dat' => [
                'items'      => $data,
                'total_cont' => 500
            ]
        ]);
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