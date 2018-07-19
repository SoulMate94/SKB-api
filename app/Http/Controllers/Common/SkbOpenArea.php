<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\SkbOpenArea as SkbOpenAreaModel;
use App\Traits\Tool;
use Illuminate\Http\Request;

class SkbOpenArea extends Controller
{
    /**
     * 获取省
     * @return $this
     */
    public function getOpenAreaProvince()
    {
        $model = new SkbOpenAreaModel;
        $data  = $model->getOpenAreaProvince();

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'ok',
            'dat' => $data
        ]);
    }

    /**
     * 根据省获得市
     * @param Request $req
     * @return $this
     */
    public function getOpenAreaCity(Request $req)
    {
        $this->validate($req, [
            'province' => 'required|numeric',
        ]);

        $province = $req->get('province');

        $model = new SkbOpenAreaModel;
        $data  = $model->getOpenAreaCity($province);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'ok',
            'dat' => $data
        ]);
    }

    /**
     * 根据市获得区
     * @param Request $req
     * @return $this
     */
    public function getOpenAreaDistrict(Request $req)
    {
        $this->validate($req, [
            'city' => 'required|numeric',
        ]);

        $city = $req->get('city');

        $model = new SkbOpenAreaModel;
        $data  = $model->getOpenAreaDistrict($city);

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'ok',
            'dat' => $data
        ]);
    }

    public function getOpenArea()
    {
        $model     = new SkbOpenAreaModel;
        $province  = $model->getOpenArea();

        foreach ($province as $v) {
            $v->city = $model->getOpenAreaCity($v->province);

            foreach ($v->city as $val) {
                $val->district = $model->getOpenAreaDistrict($val->city);
            }
        }

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'ok',
            'dat' => $province
        ]);
    }
}