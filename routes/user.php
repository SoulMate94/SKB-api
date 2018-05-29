<?php

$app->group([
    'namespace' => 'User',
], function () use ($app) {
    // 个人资料
    $app->get('get_user_info', 'SkbUser@getUserInfo');  // by caoxl

    // 净水器列表
    $app->get('get_water_cleaner_list', 'SkbWaterCleaner@getWaterCleanerList');  // by caoxl

    // 净水器详情
    $app->get('get_water_cleaner_info', 'SkbWaterCleaner@getWaterCleanerInfo');  // by caoxl
});
