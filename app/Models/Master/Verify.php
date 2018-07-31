<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Verify extends Model
{
    protected $table      = 'skb_master_verify';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    //插入master verify数据
    public function insertVerify($params)
    {
        if((!$this->checkMaster($params)) || $this->checkVerify($params)){
            return false;
        }

        $params['created_at'] = date('Y-m-d H:i:s');

        $params = $this->dealData($params);

        return $this->insertGetId($params);
    }

    //修改master verify数据
    public function updateVerify($params)
    {
        if(!($this->checkMaster($params) && $this->checkVerify($params) && $this->checkVerifyStatus($params))){
            return false;
        }

        $params = $this->dealData($params);

        return $this->where('mid',$params['mid'])->update($params);
    }

    public function skb_user()
    {
        return $this->hasOne('App\Models\User\SkbUsersModel','id', 'mid');
    }

    //数据json化处理,更新同步skb_users表
    private function dealData($params): array
    {
        $params['verify_status']    =   1;
        $params['updated_at']       =   date('Y-m-d H:i:s');

        $params['work_area']        =   json_encode($params['work_area']);
        $params['product_type_id']  =   json_encode($params['product_type_id']);
        $params['service_type_id']  =   json_encode($params['service_type_id']);
        $params['service_sta_time'] = strtotime($params['service_sta_time']);
        $params['service_end_time'] = strtotime($params['service_end_time']);

        if(isset($params['id_card_img'])) {
            $params['id_card_img']  =   json_encode($params['id_card_img']);
        }

        $skb_user['username']   =   $params['username'];
        $skb_user['mobile']     =   $params['mobile'];

        unset($params['username']);
        unset($params['mobile']);

        DB::table('skb_users')->where('id',$params['mid'])->update($skb_user);

        return $params;
    }

    /**
     * @param $params
     * @return bool
     */
    private function checkMaster($params): bool
    {
        $mid = $params['mid'];

        $res = DB::table('skb_users')->where([['id','=',$mid],['role','>',1]]);

        if ($res) {
            unset($res);
            $res = $this->where([['mid', $mid],['is_del', 0]])->first();
        }

        return $res ? true : false;
    }

    private function checkVerify($params): bool
    {
        $mid = $params['mid'];

        $res = $this->where([['mid','=',$mid]])->first();

        return $res ? true : false;
    }

    private function checkVerifyStatus($params): bool
    {
        return $this->select('verify_status')
                    ->where('mid', $params['mid'])
                    ->first()['verify_status'] ==-1?true:false;
    }
}