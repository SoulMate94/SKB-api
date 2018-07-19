<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{Tool, Session};
use App\Models\Master\SkbCash as SkbCashModel;
use App\Models\User\SkbUsersModel as SkbUsers;

class SkbCash extends Controller
{
    public function cashWithdraw(Session $ssn, SkbUsers $user, Request $req)
    {
        $this->validate($req, [
            'money'     => 'required|integer|min:0|max:999999',
            'password'  => 'required|integer|min:0|max:999999',
        ]);

        $master_id = $ssn->get('id');
        $money     = $req->get('money');
        $master    = $user->find($master_id);

        // 验证是否有该用户
        if (! $master) {
            return Tool::jsonResp([
                'err' => 4041,
                'msg' => '找不到该用户',
            ]);
        }

        // 验证旧密码
        if (!password_verify(
            $req->get('password'),
            $master->pasword
        )) {
            return Tool::jsonResp([
                'err' => 4042,
                'msg' => '请输入正确的旧密码',
            ]);
        }

    }

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