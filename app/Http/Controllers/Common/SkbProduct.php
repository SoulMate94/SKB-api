<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Tool;
use App\Models\Common\SkbProduct as SkbProductModel;

class SkbProduct extends Controller
{
    public function getProductList()
    {
        // TODO
    }

    /**
     * 通过产品分类ID获取产品
     * @param Request $req
     * @return $this
     */
    public function getProductByCateId(Request $req)
    {
        $this->validate($req, [
            'product_cate_id' => 'required|numeric',
        ]);

        $cate_id = $req->get('product_cate_id');

        $cateModel = new SkbProductModel;

        $res = $cateModel->getProductByCateId($cate_id);

        $err = $res ? 0 : 404;
        $msg = $res ? 'success' : '暂无数据';
        $dat = $res ?? [];

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);
    }
}