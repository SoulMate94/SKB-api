<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/6/21
 * Time: 下午 03:11
 */

namespace App\Http\Controllers\Orders;
use App\Traits\{
    Tool, Session, CURL
};
use Illuminate\Http\Request;

class WechatPay
{
    public function connect(Session $ssn, Request $req, CURL $curl)
    {
        $user       = $ssn->get('user');

//        $appid      = 'wxcbe1d349cf9edcaa';
//        $mch_id     = '1439831202';
//        $device_info= 'WEB';
//        $nonce_str  = md5(microtime());
//        $body       = 'JSAPI支付测试';
//        $sign_type  = 'MD5';
//        $attach     = '支付测试';
//        $detail     = '';
//        $fee_type   = 'CNY';
//        $total_fee  = '1';
//        $time_start = date('YmdHis');
//        $time_expire= date('YmdHis', time()+7200);
//        $goods_tag  = 'WXG';
//        $notify_url = 'https://skb-api.sciclean.cn/test/pay/back';
//        $trade_type = 'JSAPI';
//        $product_id = '';
//        $limit_pay  = '';
//        $openid     = $user['openid'];
//        $out_trade_no     = md5(time());
//        $spbill_create_ip = $this->get_real_ip();

        $params     = [
            'appid'     => 'wxcbe1d349cf9edcaa',
            'mch_id'    => '1439831202',
            'nonce_str' => date('Ymdhis').md5(microtime()).mt_rand(0,955867895),
            'body'      => '水可邦支付',
            'attach'    => '水可邦支付',
            'fee_type'  => 'CNY',
            'total_fee' => $req->all()['total_fee'],
            'notify_url'=> 'https://skb-api.sciclean.cn/test/pay/back',
            'trade_type'=> 'JSAPI',
            'openid'    => $user['openid'],
            'out_trade_no'  => md5(time()),
            'spbill_create_ip'    => $this->get_real_ip()
        ];

        $stringA    = '';
        $wPay       = '<xml>';
        ksort($params);
        foreach($params as $k => $v) {
            if('sign' != $k && '' != $v) {
                $stringA .= $k . '=' . $v . '&';
                $wPay    .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
        }
        $sign   = $this->getSign($stringA);
        $wPay   .='<sign>'.$sign.'</sign></xml>';

        $url    = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res    = $curl->curlPostSsl($url, $wPay);
        $res    = Tool::xmlToArray($res);

        $mobel  = [
            'appId'     => $res['appid'],
            'nonceStr'  => $res['nonce_str'],
            'package'   => 'prepay_id='.$res['prepay_id'],
            'signType'  => 'MD5',
            'timeStamp' => ''.time()
        ];
        ksort($mobel);
        $str = '';
        foreach($mobel as $k => $v) {
                $str .= $k . '=' . $v . '&';
        }
        $mobel['paySign']  = $this->getSign($str);

        return Tool::jsonR(0, 'order create success', $mobel);
    }

    /*
     * 支付回调*/
    public function back()
    {
        return 123;
    }

    public function refund(Session $ssn, CURL $curl)
    {
        $user   = $ssn->get('user');

        $params     = [
            'appid'     => 'wxcbe1d349cf9edcaa',
            'mch_id'    => '1439831202',
            'nonce_str' => md5(microtime()),
            'fee_type'  => 'CNY',
            'total_fee' => 100,
            'refund_fee'=> 100,
            'notify_url'=> 'https://skb-api.sciclean.cn/test/pay/back',
            'refund_desc'   => '水可邦用户付款',
            'transaction_id'    => '4200000153201806292573121489',
//            'out_trade_no'      => '35f3751220e4c1d7396e8b1bc4d05ee41',
            'out_refund_no'     => md5(time()),
            'spbill_create_ip'  => $this->get_real_ip()
        ];

        $stringA    = '';
        $wPay       = '<xml>';
        ksort($params);
        foreach($params as $k => $v) {
            if('sign' != $k && '' != $v) {
                $stringA .= $k . '=' . $v . '&';
                $wPay    .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
        }
        $sign   = $this->getSign($stringA);
        $wPay   .='<sign>'.$sign.'</sign></xml>';

        $url    = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $res    = $curl->curlPostSsl($url, $wPay);
        if($res){
            $res    = Tool::xmlToArray($res);
            return Tool::jsonR(0, 'refund success', $res);
        }
        return Tool::jsonR(-1, 'connect fail', '');
    }

    private function getSign($stringA)
    {
        $stringSignTemp = $stringA.'key=ywbftaesn3dy0vanhlyc5apt0l2ez02a';
        return strtoupper(MD5($stringSignTemp));
    }

    private function get_real_ip(){
        $ip=false;
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
            for ($i=0; $i < count($ips); $i++){
                if(!preg_match ('/^(10│172.16│192.168)./', $ips[$i])){
                    $ip=$ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}