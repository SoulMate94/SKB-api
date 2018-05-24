<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class SkbAddress extends Model
{
    protected $table = 'skb_address';

    public function delOnceAddress($id)
    {
        return $this->where('id', $id)->delete();
    }
}