<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'skb_orders';

    public function createOrder($res)
    {
        return $this->insertGetId($res);
    }

    public function cancel($res)
    {
        $status = $this->checkOrderStatus($res);
        if($status === false) {
            return -1;
        }
        if (in_array($status, [0, 1, 2])) {
            if ($this->update('order_status', -1)->where([
                ['order_number', $res['order_number']],
                ['uid', $res['uid']]
            ])) {
                return 0;
            }
            return -2;
        }
        return -3;
    }

    public function userRevokeOrder($res)
    {
        $status = $this->checkOrderStatus($res);
        if ($status === false) {
            return -2;
        }
        if ($status ==  3) {
            $time = $this->select('appoint_time')
                         ->where('order_number', $res['order_number'])
                         ->first()['appoint_time'];
            if(time()<($time+14400)){
                if ($this->update('order_status', -8)
                     ->where([
                         ['order_number', $res['order_number']],
                         ['uid', $res['uid']]
                     ])) {
                    return 0;
                }
                return -3;
            }
            $id = $this->update('order_status', -8)
                       ->where([
                           ['order_number', $res['order_number']],
                           ['uid', $res['uid']]
                       ]);
            //TODO 退款
            if ($id) {
                return 1;
            }
            return -3;
        }
        return -4;
    }

    public function masterRevokeOrder($res)
    {
        $status = $this->checkOrderStatus($res);
        if ($status === false) {
            return -2;
        }
        if ($status ==  3) {
            $time = $this->select('appoint_time')
                         ->where('order_number', $res['order_number'])
                         ->first()['appoint_time'];
            if(time()<($time-14400)){
                if ($this->update('order_status', -8)
                     ->where([
                         ['order_number', $res['order_number']],
                         ['mid', $res['mid']]
                     ])) {
                    return 0;
                }
                return -3;
            }
            $id = $this->update('order_status', -8)
                ->where([
                    ['order_number', $res['order_number']],
                    ['mid', $res['mid']]
                ]);
            //TODO 退款
            if ($id) {
                return 1;
            }
            return -3;
        }
        return -4;
    }

    private function checkOrderStatus($res)
    {
        $dat = $this->select('order_status')
                    ->where('order_number', $res['order_number'])
                    ->first()['order_number'];
        if($dat) {
            return $dat;
        }
        return false;
    }
}