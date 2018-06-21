<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\SkbArea;
use Illuminate\Support\Facades\DB;

class SkbOpenArea extends Model
{
    protected $table = 'skb_open_area';

    public function getOpenAreaProvince()
    {
        $res = DB::table('skb_open_area as a')
                ->select('a.id', 'a.province', 'b.name')
                ->leftjoin('skb_area as b', 'a.province', '=', 'b.id')
                ->where('a.is_open', 1)
                ->get();

        return $res;
    }

    public function getOpenAreaCity($province)
    {
        $res = DB::table('skb_open_area as a')
            ->distinct()
            ->select('a.city', 'b.name', 'b.parent_id')
            ->leftjoin('skb_area as b', 'a.city', '=', 'b.id')
            ->where('a.is_open', 1)
            ->where('b.parent_id', $province)
            ->get();

        return $res;
    }

    public function getOpenAreaDistrict($city)
    {
        $res = DB::table('skb_open_area as a')
            ->select('a.id', 'a.district', 'b.name', 'b.parent_id')
            ->leftjoin('skb_area as b', 'a.district', '=', 'b.id')
            ->where('a.is_open', 1)
            ->where('b.parent_id', $city)
            ->get();

        return $res;
    }

}