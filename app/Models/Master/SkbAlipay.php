<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SkbAlipay extends Model
{
    protected $table      = 'skb_alipay';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function getAlipayById($master_id)
    {
        $bankcard = DB::table($this->table)
                    ->select('id', 'real_name')
                    ->whereMasterId($master_id)
                    ->get();

        return $bankcard;
    }

    public function insertAlipay($params)
    {
        $res = DB::table($this->table)->insertGetId($params);

        return $res;
    }
}