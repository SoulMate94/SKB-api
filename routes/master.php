<?php

$app->group([
    'namespace' => 'Master',
], function () use ($app) {
    // 银行卡列表
    $app->get('get_bank_card_list', 'SkbBankCard@bankCardList');  // by caoxl

    // 服务类别
    $app->get('get_service_cate_list', 'SkbServiceCate@getServiceCateList');  // by caoxl

    // 个人资料
    $app->get('get_master_info', 'SkbMaster@getMasterInfo');  // by caoxl

    // 师傅认证状态
    $app->get('get_master_verify_status', 'SkbMasterVerify@index'); // by jizw

    // 师傅认证详情
    $app->get('get_master_verify_info', 'SkbMasterVerify@verifyInfo'); // by jizw

    // 师傅认证
    $app->post('post_master_verify', 'SkbMasterVerify@masterVerify'); // by jizw
});

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