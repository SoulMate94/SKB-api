<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/18
 * Time: 下午 03:26
 */
$app->group([
    'namespace' => 'Orders',
    'prefix'    => 'master/orders'
], function () use ($app) {
    // 生成订单
    $app->post('order_create', 'SkbOrder@createOrder'); // by jizw

    // 查看订单
    $app->post('order_list', 'SkbOrder@orderList'); // by jizw

    // 取消订单
    $app->post('order_cancel', 'SkbOrder@cancelOrder'); // by jizw

    // 撤销订单
    $app->post('order_revoke', 'SkbOrder@revokeOrder'); // by jizw

    // 订单状态
    $app->get('order_status', 'SkbOrder@statusOrder'); //by jizw
});