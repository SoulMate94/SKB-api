<?php

$app->get('/', function () use ($app) {
    return response()->json([
        'err' => '403',
        'msg' => 'Forbidden',
    ]);
});

// Test
$app->get('redis', 'TestController@redis');
$app->get('queue', 'TestController@queue');
$app->get('jwt', 'TestController@jwt');
$app->get('checkJwt', 'TestController@checkJwt');
$app->get('/test_jwt', function () use ($app) {
    return config('jwt');
});

$app->get('weapp/user','User\SkbUser@index');
$app->get('weapp/login','User\SkbUser@login');

$app->group([
    'prefix'=>'test'
], function ($app) {

    $app->post('login', 'User\SkbUser@testLogin');
    $app->post('create', 'Orders\Order@createOrder');
    $app->post('list', 'Orders\Order@orderList');
    $app->post('pay', 'Orders\WechatPay@connect');
    $app->get('pay/back', 'Orders\WechatPay@back');
    $app->get('pay/refund', 'Orders\WechatPay@refund');

});

$app->group([
    'prefix' => 'order'
], function ($app) {

});

$app->group([

    'prefix' => 'auth'

], function ($app) {

    $app->post('login', 'AuthController@login');
    $app->post('logout', 'AuthController@logout');
    $app->post('refresh', 'AuthController@refresh');
    $app->post('me', 'AuthController@me');

});