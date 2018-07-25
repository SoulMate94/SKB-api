<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/25
 * Time: 下午 04:22
 */

namespace App\Traits;

use App\Traits\{Tool, CURL};
use App\Traits\WeChat\{WeChatToken, FormId};

class WeChatPush
{
    public static function push($opid, $page, $temid, $dat)
    {
        $token  = new WeChatToken();
        $token  = $token->getToken();

        $fmid   = FormId::getFormId($opid);
        if(!$fmid){
            die(Tool::jsonR(-2, 'we need form_id', null));
        }

        $url    = config('service_url.wechat.template_message.send_template_message').$token;

        $data    = [];
        foreach ($dat as $k => $v){
            $data['keyword'.($k+1)]['value'] = $v;
        }

        $vars   = [
            'touser'      => $opid,
            'template_id' => $temid,
            'page'        => $page,
            'form_id'     => $fmid,
            'data'        => $data,
        ];

        $curl   = new CURL();
        $res    = $curl->curlPostSsl($url, json_encode($vars));

        if ($res) {
            return [$res,$url];
        }

        return false;
    }
}