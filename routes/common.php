<?php

$app->group([
    'namespace' => 'Common',
], function () use ($app) {
    // 文章相关
    $app->get('get_article_by_cate', 'SkbArticle@getArticleByCate');
    $app->get('get_article_info', 'SkbArticle@getArticleInfo');

    // 意见反馈
    $app->post('submit_suggest', 'SkbSuggestions@submitSuggestion');

    // 产品类别
    $app->get('get_product_cate_list', 'SkbProductCate@getProductCateList');

    // 地址相关
    $app->get('get_address', 'SkbAddress@getAddress');
    $app->post('create_or_update_address', 'SkbAddress@createOrUpdateAddress');
    $app->post('del_once_address', 'SkbAddress@delOnceAddress');
    $app->post('del_all_address', 'SkbAddress@delAllAddress');
});