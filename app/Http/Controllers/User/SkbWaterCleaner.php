<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\SkbWaterCleaner as SkbWaterCleanerModel;
use App\Traits\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkbWaterCleaner extends Controller
{
    /**
     * 净水器列表
     * @return $this
     */
    public function getWaterCleanerList()
    {
        $data = SkbWaterCleanerModel::all();
        // $data = DB::table('sciclean_price')->simplePaginate(15);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'success',
            'dat' => $data
        ]);
    }

    /**
     * 获取净水器详情
     * @param Request $req
     * @return $this
     */
    public function getWaterCleanerInfo(Request $req)
    {
        $this->validate($req, [
            'water_cleaner_id' =>'required|numeric',
        ]);

        $water_cleaner_id = $req->get('water_cleaner_id');

        $dat = SkbWaterCleanerModel::find($water_cleaner_id) ?? [];
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : '暂无数据';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);
    }
}