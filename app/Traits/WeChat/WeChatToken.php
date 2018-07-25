<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/24
 * Time: 下午 04:34
 */

namespace App\Traits\WeChat;
use App\Traits\CURL;

class WeChatToken
{
    public function getToken()
    {
        $token = config('service.wechat.access_token', null);
        $expires = config('service.wechat.expires', null);

        return $this->checkToken($token, $expires);
    }

    protected function checkToken($token, $expire)
    {
        if($token && $expire > time()){
            return $token;
        }
        return $this->getAccessToken();
    }

    protected function getAccessToken()
    {
        $url = config('service_url.wechat.access_token').
            '?grant_type=client_credential&'.
            'appid='.
            env('APPID').'&'.
            'secret='.
            env('SECRET');

        $curl   = new CURL();
        $res    = json_decode($curl->curlPostSsl($url), true);
        $token  = $res['access_token'];
        $expires= $res['expires_in'];

        config(['service.wechat.access_token'=> $token]);
        config(['service.wechat.expires'=> time()+$expires]);

        return $token;
    }
}