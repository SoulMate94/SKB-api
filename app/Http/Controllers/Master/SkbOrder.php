<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Traits\Tool;

class SkbOrder extends Controller
{
    public function getOrderList(Request $request)
    {
        $params = $request->all();

        $params['master_id'] = $request->get('master_id');

        $order = new Order;

        // TODO
        $data  = $order->getMasterAllOrder($params);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => '请求成功',
            'dat' => $data
        ]);
    }

    public function delOrderOnce(Request $request)
    {
        $params = $request->all();

        $rules  = [
            'order_id' => 'required|numeric'
        ];

        if (!$this->verifyUserParams($params, $rules)) {
            return $this->responseJson(201, $this->_msg);
        }

        $order = new Order;

        // TODO
    }
}