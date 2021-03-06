<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class SkbOrder extends Model
{
    protected $table = 'skb_orders';

    /**
     * 用户创建订单
     * @param $res
     * @return mixed
     */
    public function createOrder($res)
    {
        $dat                 = [];
        $dat['uid']          = $res['uid'];
        $dat['product_info'] = $res['product_info'];
        $dat['end_addr']     = $res['end_addr'];
        $dat['user_price']   = $res['user_price'];
        $dat['appoint_time'] = $res['appoint_time'];
        $dat['service_id']   = $res['service_id'];
        $dat['order_number'] = $res['order_number'];
        $dat['product_info'] = json_encode($res['product_info']);
        $dat['created_at']   = date('Y-m-d h:i:s');
        $dat['updated_at']   = date('Y-m-d h:i:s');

        return $this->insertGetId($dat);
    }

    /**获取师傅所有订单
     * @param $params
     */
    public function getMasterAllOrder($params)
    {
        // TODO
    }

    /**取消订单
     * @param $res
     * @return int
     */
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