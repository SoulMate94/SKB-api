<?php

$app->group([
    'namespace' => 'Common',
], function () use ($app) {
    // 文章相关
    $app->get('get_article_by_cate', 'SkbArticle@getArticleByCate');
    $app->get('get_article_info', 'SkbArticle@getArticleInfo');

    // 意见反馈
    $app->post('submit_suggest', 'SkbSuggestions@submitSuggestion');
});