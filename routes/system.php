<?php

$app->group([
    'namespace' => 'System',
    'prefix'    => 'system',
], function () use ($app) {
    // 个人资料
    $app->post('/admin/upload', 'uploadFile@uploadImage');  // by jizw

    //测试消息接口,用完即删
    $app->get('/test/message', 'Message@test');  // by jizw

//    对接微信服务器校验用接口,用完即换
    $app->get('/wechat/service', 'WeChatPushServerCheck@getWechatServiceCheck');  // by jizw

    //对接微信服务器
//    $app->get('/wechat/service', 'WeChatPushServerCheck@getWechatServiceCheck');  // by jizw

    //接收存储用户form_id专用接口
    $app->post('/wechat/push/form_id/storageFormId', 'Message@storageFormId'); //by jizw
});