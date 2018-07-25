<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/25
 * Time: 上午 09:56
 */

namespace App\Traits\WeChat;

use App\Models\System\SkbFormIdModel as FormIds;
use App\Traits\Session;

class FormId
{
    public static function getFormId($open_id = null)
    {
        $formIds= new FormIds();

        $formId = $formIds->select('form_id')
                            ->where([
                                ['open_id', '=', $open_id],
                                ['expired_time', '>', time()],
                                ['is_use', '=', '0']
                            ])
                            ->latest()
                            ->first();

        if($formId){
            return $formId->form_id;
        }

        return false;
    }

    public static function storageFormId($form_ids)
    {
        $dat = self::getOpenId();

        if($dat){
            $data= [];
            foreach ($form_ids as $v){
                $dat['form_id']     = $v['formId'];
                $dat['expired_time']= $v['expire'];
                $data[]             = $dat;
            }

            $res = FormIds::insert($data);

            return $res;
        }

        return false;
    }

    protected static function getOpenId()
    {
        $ssn    = new Session();
        $user   = $ssn->get('user');

        return [
            'user_id'   => $user['id'],
            'open_id'   => $user['openid'],
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];
    }
}