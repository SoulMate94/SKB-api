<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\SkbOpenArea as SkbOpenAreaModel;
use App\Traits\Tool;
use Illuminate\Http\Request;

class SkbOpenArea extends Controller
{
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
}