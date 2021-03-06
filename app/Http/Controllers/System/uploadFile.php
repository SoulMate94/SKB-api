<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/20
 * Time: 下午 02:09
 */

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Traits\{Tool, Session};
use Illuminate\Http\Request;

class uploadFile extends Controller
{
    public function uploadImage(Request $req)
    {
        $folder   = $req->get('folder');
        $identity = $req->get('identity') == 1 ? 'master':'user';

        if(!$req->hasFile('skbPublicFile')) {
            return Tool::jsonR(-1,'File error', null);
        }
        $files = $req->file('skbPublicFile');

        if(is_array($files)) {
            foreach ($files as $k => $value){
                $fileName = 'skb_'
                            .time()
                            .rand(1000, 9999)
                            .'.'
                            .$value->getClientOriginalExtension();

                $folder_tmp   = $folder.'/'.$identity;
                if($value->move('/var/www/skb/skbApi/public/uploads/' . $folder_tmp, $fileName)){
                    $path[$k] = '/uploads/'
                                .$folder_tmp
                                .'/'
                                .$fileName;
                }
            }

            return Tool::jsonR(0,'success',$path);
        }

        $fileName = 'skb_'.time().rand(1000, 9999).'.'.$files->getClientOriginalExtension();
        $folder_tmp   = $folder
                        .'/'
                        .$identity;
        if($files->move('/var/www/skb/skbApi/public/uploads/' . $folder_tmp, $fileName)){
            $path[] = '/uploads/'
                    . $folder_tmp
                    . '/'
                    . $fileName;
        }

        return Tool::jsonR(0,'success',$path);
    }

    public function masterVerifyUploadImage(Request $req, Session $ssn)
    {
        if($ssn->get('user')){
            if(!$req->hasFile('skbMasterUploadImage')) {
                return Tool::jsonR(-1,'File error', null);
            }
            $files = $req->file('skbMasterUploadImage');

            $fileName = 'skb_'
                .time()
                .rand(1000, 9999)
                .'.'
                .$files->getClientOriginalExtension();

            $folder_tmp = date('Ymd');
            if($files->move('/var/www/skb/skbAdmin/public/uploads/masterVerify/'
                            .$folder_tmp, $fileName)){
                $path   = 'uploads/masterVerify/'
                    .$folder_tmp
                    .'/'
                    .$fileName;

                return Tool::jsonR(0,'success',$path);
            }
        }

        return Tool::jsonR(-1, 'you need login', null);
    }
}