<?php

$app->group([
    'namespace' => 'System',
    'prefix'    => 'system',
], function () use ($app) {
    // 个人资料
    $app->get('/admin/upload/{file}/{folder}/{identity}', 'uploadFile@uploadImage');  // by caoxl
});