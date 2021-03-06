<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Traits\{Tool, Session};
use App\Models\Master\SkbMaster as SkbMasterModel;

class SkbMaster extends Controller
{
    /**
     * 获取师傅信息
     * @param Session $ssn
     * @return $this
     */
    public function getMasterInfo(Session $ssn)
    {
        $master_id = $ssn->get('id');

        if ($master_id && is_int($master_id)) {
            $dat = SkbMasterModel::where('mid', $master_id)->first();
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

    public function updateMasterInfo($masterId)
    {
        return $masterId;
    }


    /**
     * 切换师傅接单状态
     * @param $master_id
     * @return bool
     */
    public function switchWorkStatus($master_id)
    {
        // TODO
        return true;
    }
}