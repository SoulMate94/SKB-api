<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/5/11
 * Time: 下午 03:38
 */
return [
    'wechat'=>[
        'appId'          => env('WEIXIN_KEY'),
        'appSecret'      => env('WEIXIN_SECRET'),
        'useQcloudLogin' => false,
        'mysql' => [
            'host' => env('DB_HOST','127.0.0.1'),
            'port' => env('DB_PORT',3306),
            'user' => env('DB_USERNAME','root'),
            'pass' => env('DB_PASSWORD',''),
            'db'   => env('DB_DATABASE','weSdk'),
            'char' => 'utf8mb4'
        ],
        'cos' => [
            'region'       => 'cn-south',
            'fileBucket'   => 'wafer',
            'uploadFolder' => ''
        ],
        // 微信登录态
        'wxLoginExpires' => 7200,
        'wxMessageToken' => 'abcdefgh',
        'template_id'    => [
            'orderPaymentSuccess'  => 'uWnGobxPZ2lqFgpWvAI_ZrFpYFJZkYDEsqVGb86I_oU',  //订单支付成功通知
            'reviewDidNotPass'     => 'lOyr3DRwDWvSSxDcmgTb-YdwQzYvVPHmXsj8ODb9gxw',   //审核失败通知
            'messageNotification'  => '23Vf0IWo4_5R_rLVcWDQv530W11755_h8bxamBa6lQA'   //通用消息通知
        ]
    ]
];