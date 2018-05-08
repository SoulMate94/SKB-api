<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SkbArticle extends Model
{
    protected $table      = 'skb_article';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $page   = 10;

    public function getArticleByCateId($cateId)
    {
        $article = DB::table($this->table)
                    ->where('cate_id', "$cateId")
                    ->orderBy('order', 'DESC')
                    ->get();

        return $article;
    }
}