<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

class SkbMaster extends Controller
{
    public function masterInfo()
    {
        return 'master info';
    }

    public function updateMasterInfo($masterId)
    {
        return $masterId;
    }

    public function verifyMaster()
    {
        return 'master verify';
    }
}