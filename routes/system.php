<?php

$app->group([
    'namespace' => 'System',
    'prefix'    => 'system',
], function () use ($app) {
    // 个人资料
    $app->post('/admin/upload/{folder}/{identity}', 'uploadFile@uploadImage');  // by caoxl
});