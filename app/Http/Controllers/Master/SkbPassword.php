<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\{Tool, Session};
use App\Models\User\SkbUsersModel as SkbUsers;

class SkbPassword extends Controller
{

    /**
     * 师傅端设置提现密码
     * @param Session $ssn
     * @param SkbUsers $user
     * @param Request $req
     * @return $this
     */
    public function setMasterPassword(Session $ssn, SkbUsers $user, Request $req)
    {
        $this->validate($req, [
            'password'  => 'required|integer|min:0|max:999999',
        ]);

        $master_id = $ssn->get('id');
        $password  = $req->get('password');

        $setSuccess = $user
        ->whereId($master_id)
        ->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        if (in_array($setSuccess, [0, 1])) {
            $err = 0;
            $msg = 'Set Success';
        } else {
            $err = 5032;
            $msg = 'Set failed';
        }

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
        ]);
    }

    /**
     * 师傅端修改提现密码
     * @param Session $ssn
     * @param SkbUsers $user
     * @param Request $req
     * @return $this
     */
    public function updateMasterPassword(Session $ssn, SkbUsers $user, Request $req)
    {
        $this->validate($req, [
            'password'  => 'required|integer|min:0|max:999999',
            'new_password' => 'required|integer|min:0|max:999999',
            'renew_password' => 'required|integer|min:0|max:999999',
        ]);

        $master_id = $ssn->get('id');
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

        // 验证新密码
        if (($new_password = $req->get('new_password')) != ($req->get('renew_password'))) {
            return Tool::jsonResp([
                'err' => 4031,
                'msg' => '两次输入的提现密码不匹配',
            ]);
        } elseif (6 != mb_strlen($new_password)) {
            return Tool::jsonResp([
                'err' => 4032,
                'msg' => '请先设置6位数的提现密码',
            ]);
        }

        // 更新密码
        $updateSuccess = $user
        ->whereId($master_id)
        ->update([
            'password' => password_hash($new_password, PASSWORD_DEFAULT)
        ]);

        if (in_array($updateSuccess, [0, 1])) {
            $err = 0;
            $msg = 'ok';
        } else {
            $err = 5032;
            $msg = 'failed';
        }

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
        ]);
    }
}