<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Tool;
use App\Models\Master\SkbCash as SkbCashModel;

class SkbCash extends Controller
{
    /**
     * 师傅端提现记录
     * @param Request $request
     * @return $this
     */
    public function cashDetail(Request $request)
    {
        $params = $request->all();

        if (!isset($params['status'])) {
            return Tool::jsonResp([
                'err' => 201,
                'msg' => '参数错误',
                'dat' => ''
            ]);
        }

        $model = new SkbCashModel;
        $data  = $model->masterCashData($request->get('master_id'), $params);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'success',
            'dat' => $data
        ]);
    }
}