<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class RegisterModel extends Model
{
    protected $table      = 'skb_master';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}