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

    public static function storageFormId($open_id, $form_ids)
    {
        $dat = self::getOpenId($open_id);

        if($dat){
            $data= [];
            foreach ($form_ids as $v){
                $dat['form_id']     = $v['form_id'];
                $dat['expired_time']= $v['expired_time'];
                $data[]             = $dat;
            }

            FormIds::insert($data);

            return true;
        }

        return false;
    }

    protected static function getOpenId($open_id)
    {
        $ssn    = new Session();
        $user   = $ssn->get('user');

        if($open_id === $user['openid']){
            return [
                'user_id' => $user['id'],
                'open_id' => $user['openid']
            ];
        }

        return false;
    }
}