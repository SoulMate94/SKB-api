<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\Tool;
use App\Models\User\SkbUsersModel as SkbUsers;
use QCloud_WeApp_SDK\Conf as Config;
use QCloud_WeApp_SDK\Auth\LoginService as LoginService;
use QCloud_WeApp_SDK\Constants as Constants;

class SkbUser extends Controller
{
    public function __construct()
    {
        Config::setup(config('services.wechat'));
    }

    public function index() {
        $result = LoginService::check();

        if ($result['loginState'] === Constants::S_AUTH) {
            return Tool::jsonResp([
                'err' => 0,
                'dat' => $result['userinfo']
            ]);
        } else {
            return Tool::jsonResp([
                'err' => -1,
                'msg' => []
            ]);
        }
    }

    public function login() {
        $result = LoginService::login();

        if ($result['loginState'] === Constants::S_AUTH) {

            $users  = new SkbUsers();

            $user   = $users->where('openid','=',$result['userinfo']['userinfo']->openId)->first();

            if($user){
                return Tool::jsonResp([
                    'err' => 0,
                    'dat' => $user
                ]);
            }

            //注册用户 by jizw
            $user = ['username'  =>  '',
                'openid'    =>  $result['userinfo']['userinfo']->openId,
                'nickname'  =>  $result['userinfo']['userinfo']->nickName,
                'avatar'    =>  $result['userinfo']['userinfo']->avatarUrl,
                'created_at'=>  date('Y-m-d H:i:s'),
                'updated_at'=>  date('Y-m-d H:i:s')];
            $users->insert($user);

            return Tool::jsonResp([
                'err' => 0,
                'dat' => $user
            ]);
        }
            return Tool::jsonResp([
                'err' => -1,
                'msg' => $result['error']
            ]);
    }
}