<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/24
 * Time: 上午 10:13
 */

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;

class WeChatPushServerCheck
{
    public function getWechatServiceCheck(Request $req)
    {
        $signature  = $req->get('signature');
        $timestamp  = $req->get('timestamp');
        $nonce      = $req->get('nonce');

        if ($this->checkSignature($signature, $timestamp, $nonce)) {
            return $req->get('echostr');
        }

        return false;
    }

    private function checkSignature($signature, $timestamp, $nonce)
    {
        $token = env('WECHAT_SERVICE_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}