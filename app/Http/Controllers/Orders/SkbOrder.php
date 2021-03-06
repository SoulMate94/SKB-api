<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\{
    Common\SkbProduct,
    Orders\SkbOrder as OrderModel,
    Master\Verify
};
use App\Traits\Session;
use App\Traits\Tool;
use Illuminate\Http\Request;

class SkbOrder extends Controller
{

    /**
     * 下单;用户端创建订单
     * @param Request $req
     * @param Session $ssn
     * @param OrderModel $order
     * @return $this
     */
    public function createOrder(Request $req, Session $ssn, OrderModel $order)
    {
        $res = $req->all();

        $this->validate($req,[
            'uid'          => 'required|numeric',
            'product_info' => 'required|array',
            'end_addr'     => 'required|numeric',
            'user_price'  => 'required|numeric',
            'appoint_time' => 'required|numeric',
            'service_id'   => 'required|numeric',
//            'area_id'      => 'required|array'
        ]);

        //用户校验
        if ($ssn->get('user')['id'] != $res['uid']) {
            return Tool::jsonR(-2, 'uid error', '');
        }

        //验证价格的正确性
        $price_tmp = [];
        foreach ($res['product_info'] as $v) {
            //提取提交产品id
            $price_tmp[] = $v['product_id'];
        }
        $prices    = SkbProduct::select(['install_price','uninstall_price'])
                                    ->whereIn('id',$price_tmp)
                                    ->get();

        if($prices->isEmpty()) return Tool::jsonR(-4,'product_info is error', null);

        //统计价格
        $price_tmp = 0;
        switch($res['service_id'])
        {
        case 1:
            $product = 'install_price';
            break;
        case 2:
            $product = 'uninstall_price';
            break;
        default :
            return Tool::jsonR(-5, '服务超出范围', null);
        }

        foreach ($prices->toArray() as $v) {
            $price_tmp += $v[$product];
        }
        //检测价格是否正常
        if ($price_tmp != $res['user_price']) {
            return Tool::jsonR(-3, 'price error', '');
        }

//        $res['area_id']      = json_encode($res['area_id']);
        $res['order_number'] = trade_no();

        if ($order->createOrder($res)) {
            return Tool::jsonR( 0, 'create success', [
                'order_number' => $res['order_number']
            ]);
        }

        return Tool::jsonR( -1, '服务器目前有些繁忙,请稍后再试', '');
    }

    /**
     * 获取订单列表 师傅端 by jizw
     * @param Session $ssn
     * @param OrderModel $orders
     * @param Verify $verify
     * @param SkbProduct $proModel
     * @return $this
     */
    public function getOrders(
        Session $ssn,
        OrderModel $orders,
        Verify $verify,
        SkbProduct $proModel
    ){
        $usr   = $ssn->get('user');
        if ($usr['role'] != 2) return Tool::jsonR(-1, '这个操作只有师傅才可以进行', '');

        // 获取师傅工作区域
        $areas = $verify->getMasterAreas($usr['id']);

        if(!$areas) return Tool::jsonR(-2, 'work_area is fail', null);

        $orders = $orders->select([
                            'skb_orders.id',
                            'skb_orders.uid',
                            'skb_orders.order_number',
                            'skb_orders.product_info',
                            'skb_orders.service_id',
                            'skb_orders.user_price',
                            'skb_orders.appoint_time',
                            'skb_orders.end_addr',
                            'skb_orders.order_remarks',
                            'skb_orders.master_price',
                            'user.id as user_id',
                            'user.username',
                            'user.nickname',
                            'user.avatar'
                        ])
                ->where('order_status', 0)
//                ->where('appoint_time', '>', time()+7200)
                ->whereIn('end_addr', $areas)
                ->leftjoin('skb_users as user', 'skb_orders.uid', '=', 'user.id')
                ->get();

        if($orders->isEmpty()) return Tool::jsonR(1, '没有符合条件的订单', null);

        //获取产品详情
        $proInfo  = $orders->pluck('product_info')
                    ->toArray();
        $proInfos = $proModel->getProducInfoByOrder($proInfo);

        if(!$proInfos) return Tool::jsonR(-3, 'product error', null);

        $orders   = $orders->toArray();

        return Tool::jsonR(0,
            'get orderList success',
                [
                    'orders'    => $orders,
                    'product'   => $proInfos
                ]);
    }

    /**
     * 快速接单,师傅端
     */
    public function quickPlaceOrder(Request $req, Session $ssn, OrderModel $order)
    {
        $this->validate(['uid' => 'require|number']);

        $uid    = $req->post('uid');
        $user   = $ssn->get('user');

        if ($uid == $user['id']) {
            // TODO
        }

        return Tool::jsonR(-1, 'your uid is wrong', null);
    }

    /**
     * 取消订单;用户端,师傅端
     */
    public function cancelOrder(Request $req, Session $ssn, OrderModel $order)
    {
        $this->validate($req,[
            'uid'          => 'required|numeric',
            'order_number' => 'required',
        ]);

        $res = $req->all();

        if ($res['uid'] == $ssn->get('user')['id']) {
            $result = $order->cancel($res);
            switch ($result) {
                case 0 :
                    return Tool::jsonR(0, 'cancel order success', '');
                case -1 :
                    return Tool::jsonR(-2, 'order_number error', $res['order_number']);
                case -2 :
                    return Tool::jsonR( -3, '服务器异常', '');
                case -3 :
                    return Tool::jsonR( -4, '该订单状态不可修改', '');
            }
        }

        return Tool::jsonR(-1, '用户信息异常', $res['uid']);
    }

    /**
     * 撤销订单;用户端,师傅端
     */
    public function revokeOrder(Request $req, Session $ssn, OrderModel $order)
    {
        $this->validate($req,[
            'uid'          => 'numeric',
            'order_number' => 'required',
        ]);

        $res = $req->all();

        $usr = $ssn->get('user');

        switch ($usr['role']) {
            case 1 :
                //用户撤单 费用扣除部分还处于 TODO
                if ($res['uid']&&$res['uid'] == $usr['id']) {
                    $result = $order->userRevokeOrder($res);
                    switch ($result) {
                        case 0 :
                            return Tool::jsonR(0, '用户撤单成功', '');
                        case 1 :
                            return Tool::jsonR(1, '用户撤单成功,已扣除相关费用', '');
                        case -2 :
                            return  Tool::jsonR(-2, '订单编号异常', $res['order_number']);
                        case -3 :
                            return Tool::jsonR(-3, '服务器异常', $res['order_number']);
                        case -4 :
                            return Tool::jsonR(-4, '该订单状态不可进行这种操作', '');
                    }
                }

                return Tool::jsonR(-1, '用户信息异常', $res['uid']);
            case 2 :
                //师傅撤单 费用扣除部分还处于 TODO
                if ($res['mid']&&$res['mid'] == $usr['id']) {
                    $result = $order->masterRevokeOrder($res);
                    switch ($result) {
                        case 0 :
                            return Tool::jsonR(0, '师傅撤单成功', '');
                        case 1 :
                            return Tool::jsonR(1, '师傅撤单成功,已扣除相关费用', '');
                        case -2 :
                            return  Tool::jsonR(-2, '订单编号异常', $res['order_number']);
                        case -3 :
                            return Tool::jsonR(-3, '服务器异常', $res['order_number']);
                        case -4 :
                            return Tool::jsonR(-4, '该订单状态不可进行这种操作', '');
                    }
                }

                return Tool::jsonR(-1, '师傅信息异常', $res['mid']);
            default :
                return Tool::jsonR(-10, '用户状态异常', '');
        }
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
    public function receiveOrder(Request $req, Session $ssn, OrderModel $order)
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
        if (!in_array($status, [-1,0,1,2,3,8,-8])) {
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

        return Tool::jsonR(0, '获取订单列表成功', [
            'items'      =>  $data,
            'total_cont' => 500
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