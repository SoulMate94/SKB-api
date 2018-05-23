<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\SkbAddress as SkbAddressModel;
use App\Traits\{Tool, Session};

class SkbAddress extends Controller
{
    public function getAddress(Session $ssn)
    {
        $uid = $ssn->get('id');
        $uid = 1;
        if ($uid && is_int($uid)) {
            $dat = SkbAddressModel::where('uid', $uid)->get();
        }

        $dat = $dat ?? [];
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : 'fail';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat
        ]);
    }

    public function createOrUpdateAddress()
    {
        // TODO
        return 'createOrUpdateAddress';
    }


    public function delOnceAddress()
    {
        // TODO
        return 'delOnceAddress';
    }

    public function delAllAddress()
    {
        // TODO
        return 'delAllAddress';
    }
}