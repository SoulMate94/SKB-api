<?php

// Simple CURL Helper functions
// @caoxl

namespace App\Traits;

trait CURL
{
    public function requestJsonApi(
        $uri,
        $type = 'POST',
        $params = [],
        $headers = []
    ) {
        $headers = [
            'Content-Type: application/json; Charset=UTF-8',
        ];

        $res = $this->requestHTTPApi($uri, $type, $headers, $params);

        if (!$res['err']) {
            $res['res'] = json_decode($res['res'], true);
        }

        return $res;
    }

    public function requestHTTPApi(
        string $uri,
        string $type = 'GET',
        array $headers = [],
        $data
    ) {
        $setOpt = [
            CURLOPT_URL            => $uri,
            CURLOPT_RETURNTRANSFER => true,
        ];

        if ($headers) {
            $setOpt[CURLOPT_HTTPHEADER] = $headers;
        }

        if ('POST' == $type) {
            $setOpt[CURLOPT_POST]       = true;
            $setOpt[CURLOPT_POSTFIELDS] = $data;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $setOpt);

        $res = curl_exec($ch);

        $errNo  = curl_errno($ch);
        $errMsg = curl_error($ch);

        curl_close($ch);

        return [
            'err' => $errNo,
            'msg' => ($errMsg ?: 'ok'),
            'res' => $res,
        ];
    }

    /**微信服务器通信
     * @param string $url
     * @param $vars
     * @param int $second
     * @param array $aHeader
     * @return bool|mixed
     */
    public function curlPostSsl(
        string $url,
        $vars,
        int $second = 30,
        array $aHeader = []
    ) {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,'/etc/pem/apiclient_cert.pem');
//        curl_setopt($ch,CURLOPT_SSLCERT,dirname(getcwd()).'/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,'/etc/pem/apiclient_key.pem');
//        curl_setopt($ch,CURLOPT_SSLKEY,dirname(getcwd()).'/apiclient_key.pem');

        //第二种方式，两个文件合成一个.pem文件
//        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        $error = curl_errno($ch);
        echo "call faild, errorCode:$error\n";
        curl_close($ch);
        return false;
    }
}