<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

class SkbHomePage extends Controller
{
    /**
     * 获取信用分
     * @param $master_id
     * @return int
     */
    public function getCreditScore($master_id)
    {
        // TODO
        return 100;
    }

    /**
     * 获取已赚取金额
     * @param $master_id
     * @return int
     */
    public function getEarnedMoney($master_id)
    {
        // TODO
        return 100;
    }

    /**
     * 获取总接单数
     * @param $master_id
     * @return int
     */
    public function getOrderNumber($master_id)
    {
        // TODO
        return 100;
    }
}