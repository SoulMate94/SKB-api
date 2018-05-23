<?php

$app->group([
    'namespace' => 'Master',
], function () use ($app) {
    // 银行卡列表
    $app->get('get_bank_card_list', 'SkbBankCard@bankCardList');

    // 服务类别
    $app->get('get_service_cate_list', 'SkbServiceCate@getServiceCateList');

    // 个人资料
    $app->get('get_master_info', 'SkbMaster@getMasterInfo');
});