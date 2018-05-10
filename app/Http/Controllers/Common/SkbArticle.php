<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Common\SkbArticle as SkbArticleModel;
use App\Models\Common\SkbArticleCate;
use App\Traits\Tool;

class SkbArticle extends Controller
{
    /**
     * 根据文章分类查询文章
     * @param Request $req
     * @return $this
     */
    public function getArticleByCate(Request $req)
    {
        $this->validate($req, [
            'cate_name' => 'required',
        ]);

        $cate_name = $req->get('cate_name');

        $cateModel = new SkbArticleCate;

        if ($cate_name) {

            $res = $cateModel->select('id')
                             ->whereTitle($cate_name)
                             ->first();

            if (!is_null($res)) {

                $articleModel = new SkbArticleModel();

                $article = $articleModel->getArticleByCateId($res->id);
            } else {
                return Tool::jsonResp([
                    'err' => '404',
                    'msg' => '参数错误',
                    'dat' => [],
                ]);
            }
        }

        $err = $article ? 0 : 404;
        $msg = $article ? 'success' : '暂无数据';
        $dat = $article ?? [];

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);

    }

    /**
     * 查询文章详情
     * @param Request $req
     * @return $this
     */
    public function getArticleInfo(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|numeric',
        ]);

        $article_id = $req->get('id');

        $dat = SkbArticleModel::find($article_id) ?? [];
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : '暂无数据';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);
    }

}