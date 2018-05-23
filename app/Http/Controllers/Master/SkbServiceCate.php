<?php

namespace APP\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Traits\Tool;
use Illuminate\Support\Facades\DB;

class SkbServiceCate extends Controller
{
    public function getServiceCateList()
    {
        $dat = DB::table('skb_service_cate')
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