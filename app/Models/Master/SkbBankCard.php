<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SkbBankCard extends Model
{
    protected $table      = 'skb_bank_card';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function getBankCardListById($master_id)
    {
        $bankcard = DB::table($this->table)
                    ->select('id', 'real_name', 'bank_name', 'bank_card_number', 'card_type_name')
                    ->whereMasterId($master_id)
                    ->get();

        return $bankcard;
    }

    public function insertBankCard($params)
    {
        $res = DB::table($this->table)->insertGetId($params);

        return $res;
    }
}