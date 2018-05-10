<?php

$app->group([
    'namespace' => 'Master',
], function () use ($app) {
    // 银行卡
    $app->get('get_bank_card_list', 'SkbBankCard@bankCardList');
});