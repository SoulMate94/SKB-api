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
        // 微信登录态有效期
        'wxLoginExpires' => 7200,
        'wxMessageToken' => 'abcdefgh'
    ]
];