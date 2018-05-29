<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SkbProduct extends Model
{
    protected $table = 'skb_product';

    public function getProductByCateId($cate_id)
    {
        $Product = DB::table($this->table)
            ->select('product_cate_id', 'product_name', 'product_price', 'product_img', 'product_exp')
            ->where('product_cate_id',"$cate_id")
            ->where('is_active', '1')
            ->get()
            ->toArray();

        return $Product;
    }
}