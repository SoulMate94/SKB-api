<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\Tool;
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
                'code' => 0,
                'data' => $result['userinfo']
            ]);
        } else {
            return Tool::jsonResp([
                'code' => -1,
                'data' => []
            ]);
        }
    }

    public function login() {
        $result = LoginService::login();

        if ($result['loginState'] === Constants::S_AUTH) {
            return Tool::jsonResp([
                'code' => 0,
                'data' => $result['userinfo']
            ]);
        } else {
            return Tool::jsonResp([
                'code' => -1,
                'error' => $result['error']
            ]);
        }
    }
}