<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkbUsersModel extends Model
{
    use SoftDeletes;

    protected $table = 'skb_users';

    public function getUserInfoByOrder(
        $userId,
        $condition = [
            'id',
            'username',
            'nickname',
            'avatar'
           ])
    {
        $users  = $this->select($condition)
            ->where('is_del', 0)
            ->whereIn('id', $userId)
            ->get();

        if($users->isEmpty())return false;

        $users  = $users->toArray();
        $userInfo = [];
        foreach ($users as $user)
        {
            $userInfo[$user['id']] = $user;
            unset($userInfo[$user['id']][0]);
        }

        return $userInfo;
    }
}