<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SkbArticleCate extends Model
{
    protected $table      = 'skb_article_cate';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function getCateIdByTitle($title)
    {
        $cate = DB::table($this->table)
                    ->select('id')
                    ->where('title', "$title")
                    ->first();

        return $cate;
    }

    public function getArticleCateList()
    {
        $cate = DB::table($this->table)
            ->orderby('order')
            ->get();

        return $cate;
    }
}