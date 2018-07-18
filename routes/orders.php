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
    $app->post('order_create', 'Order@createOrder'); // by jizw

    // 查看订单
    $app->post('order_list', 'Order@orderList'); // by jizw

    // 取消订单
    $app->post('order_list_cancel', 'Order@cancelOrder'); // by jizw

    // 撤销订单
    $app->post('order_revoke', 'Order@revokeOrder'); // by jizw

    // 订单状态
    $app->get('order_status', 'Order@statusOrder'); //by jizw
});