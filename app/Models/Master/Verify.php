<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Verify extends Model
{
    protected $table      = 'skb_master_verify';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    public function insertVerify($params)
    {
        if(!$this->checkMaster($params)){
            return false;
        }

        $params['work_area']        =   json_encode($params['work_area']);
        $params['id_card_img']      =   json_encode($params['id_card_img']);
        $params['product_type_id']  =   json_encode($params['product_type_id']);
        $params['service_type_id']  =   json_encode($params['service_type_id']);

        $skb_user['username']   =   $params['username'];
        $skb_user['mobile']     =   $params['mobile'];

        unset($params['username']);
        unset($params['mobile']);

        $vId    =   $this->insertGetId($params);
        $uId    =   DB::table('skb_users')->where('id',$params['mid'])->update($skb_user);

        return ['verifyId'=>$vId,'skbUserId'=>$uId];
    }

    public function skb_user()
    {
        return $this->hasOne('App\Models\User\SkbUsersModel','id', 'mid');
    }

    /**
     * @param $params
     * @return bool
     */
    private function checkMaster($params): bool
    {
        $mid = $params['mid'];


        $res = DB::table('skb_users')->where([['id','=',$mid],['role','>',1]]);

        return $res ? true : false;
    }
}