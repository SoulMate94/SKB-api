<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Traits\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkbSuggestions extends Controller
{
    public function submitSuggestion(Request $req)
    {
        $this->validate($req, [
            'master_name'   => 'required',
            'master_mobile' => [
                'required',
                'numeric',
                'regex:/^1[3-9][0-9]{9}$/'
            ],
            'suggest_content' => 'required',
        ]);

        $master_name     = $req->get('master_name');
        $master_mobile   = $req->get('master_mobile');
        $suggest_content = $req->get('suggest_content');

        $dat = DB::table('skb_suggestions')->insert([
            'feedback_name'    => $master_name,
            'feedback_mobile'  => $master_mobile,
            'feedback_content' => $suggest_content,
            'created_at'       => date('Y-m-d H:i:s', time()),
            'updated_at'       => date('Y-m-d H:i:s', time()),
        ]);

        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : 'fails';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);
    }
}