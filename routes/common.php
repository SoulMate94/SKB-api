<?php

$app->group([
    'namespace' => 'Common',
], function () use ($app) {
    // 文章相关
    $app->get('get_article_cate_list', 'SkbArticle@getArticleCateList'); // by caoxl
    $app->get('get_article_by_cate_name', 'SkbArticle@getArticleByCateName');  // by caoxl
    $app->get('get_article_by_cate_id', 'SkbArticle@getArticleByCateId');  // by caoxl
    $app->get('get_article_info', 'SkbArticle@getArticleInfo');  // by caoxl

    // 地址相关
    $app->get('get_address', 'SkbAddress@getAddress');  // by caoxl
    $app->post('create_or_update_address', 'SkbAddress@createOrUpdateAddress'); // by caoxl
    $app->post('del_once_address', 'SkbAddress@delOnceAddress');  // by caoxl
    $app->post('del_all_address', 'SkbAddress@delAllAddress');  // by caoxl

    // 地址区域
    $app->get('get_open_area_province', 'SkbOpenArea@getOpenAreaProvince'); // by caoxl
    $app->get('get_open_area_city', 'SkbOpenArea@getOpenAreaCity'); // by caoxl
    $app->get('get_open_area_district', 'SkbOpenArea@getOpenAreaDistrict'); // by caoxl
    $app->get('get_open_area', 'SkbOpenArea@getOpenArea'); // by caoxl

    // 产品相关
    $app->get('get_product_cate_list', 'SkbProductCate@getProductCateList');  // by caoxl
    $app->get('get_product_by_cate_id', 'SkbProduct@getProductByCateId'); // by caoxl

    // 意见反馈
    $app->post('submit_suggest', 'SkbSuggestions@submitSuggestion');  // by caoxl
});