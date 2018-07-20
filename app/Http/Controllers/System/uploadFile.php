<?php
/**
 * Created by PhpStorm.
 * User: j5521
 * Date: 2018/7/20
 * Time: 下午 02:09
 */

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller,
    App\Traits\Tool;
use Illuminate\Http\Request;

class uploadFile extends Controller
{
    public function uploadImage(Request $req)
    {
        $folder   = $req->get('folder');
        $identity = $req->get('identity') == 1 ? 'master':'user';
        return $req->get('identity');

        if(!$req->hasFile('skbPublicFile')) {
            return Tool::jsonR(-1,'File error', null);
        }
        $files = $req->file('skbPublicFile');

        if(is_array($files)) {
            foreach ($files as $k => $value){
                $fileName = 'skb_'.time().rand(1000, 9999).'.'.$value->getClientOriginalExtension();
                $folder_tmp   = $folder.'/'.$identity;
                if($value->move('/var/www/skb/skbApi/public/'.$folder_tmp, $fileName)){
                    $path[$k] = $folder_tmp.'/'.$fileName;
                }
            }

            return Tool::jsonR(0,'success',$path);
        }

        $fileName = 'skb_'.time().rand(1000, 9999).'.'.$files->getClientOriginalExtension();
        $folder_tmp   = $folder.'/'.$identity;
        if($files->move('/var/www/skb/skbApi/public/'.$folder_tmp, $fileName)){
            $path[] = $folder_tmp.'/'.$fileName;
        }

        return Tool::jsonR(0,'success',$path);
    }

}