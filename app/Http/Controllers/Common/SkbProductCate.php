<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Traits\Tool;
use Illuminate\Support\Facades\DB;

class SkbProductCate extends Controller
{
    public function getProductCateList()
    {
        $dat = DB::table('skb_product_cate')
            ->select('id', 'title')
            ->where('is_active', 1)
            ->get();

        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : '暂无数据';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat
        ]);
    }
}