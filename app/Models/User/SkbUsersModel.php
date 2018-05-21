<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkbUsersModel extends Model
{
    use SoftDeletes;

    protected $table = 'skb_users';
}