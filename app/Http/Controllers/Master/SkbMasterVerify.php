<?php

namespace App\Http\Controllers\Master;


use App\Http\Controllers\Controller,
    App\Traits\Tool,
    App\Traits\Session,
    App\Models\Master\Verify as masterVerify;
use Illuminate\Http\Request,
    Illuminate\Support\Facades\Validator;

class SkbMasterVerify extends Controller
{

    public function index()
    {

    }

    /**
     * 师傅认证
     * @param Request $req
     * @return $this
     */
    public function verifyList(Request $req, masterVerify $verify)
    {
        $this->validate($req, [
            'master_id' => 'required|numeric',
        ]);

        $master_id = $req->get('master_id');

        $dat = $verify->select('verify_status','failure_reason')->where('id',$master_id)->first();
        if ($dat['verify_status'] === 2) {
            return Tool::jsonResp([
                'err' => 0,
                'msg' => '认证成功',
                'dat' => $dat,
            ]);
        }

        if($dat['verify_status'] === 1) {
            return Tool::jsonResp([
                'err' => 1,
                'msg' => '认证中,稍后我们的工作人员会联系你',
                'dat' => ['verify_status' =>$dat['verify_status']],
            ]);
        }

        return Tool::jsonResp([
            'err' => -1,
            'msg' => $dat['failure_reason'],
            'dat' => $dat,
        ]);

    }

    /**
     * 提交认证表单
     * @param Request $req
     * @param masterVerify $verify
     * @return $this
     */
    public function masterVerify(Session $ssn, Request $req, masterVerify $verify)
    {
        $params = $req->all();

        $rules  =  [
            'username'         =>  'required|string',
            'id_number'         =>  [
                'required',
                'regex:/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/',
            ],
            'mobile'            =>  [
                'required',
                'regex:/0?(13|14|15|17|18|19)[0-9]{9}/'
            ],
            'work_year'         =>  'required|numeric',
            'work_area'         =>  'required|array',
            'id_card_img'       =>  'required|array',
            'product_type_id'   =>  'required|array',
            'service_type_id'   =>  'required|array',
            'service_sta_time'  =>  'required|numeric',
            'service_end_time'  =>  'required|numeric',
        ];

        if ($msg = $this->check($params, $rules)) {
            return Tool::jsonResp([
                'err' => '403',
                'msg' => $msg,
            ]);
        }

        $params['mid']          =   $ssn->get('user')['id'];
        $params['id_card_img']  =   $this->masterVerifyFile($req);
        $params['verify_status']=   1;
        $params['created_at']   =   date('Y-m-d H:i:s');
        $params['updated_at']   =   date('Y-m-d H:i:s');

        $dat = $verify->insertVerify($params);

        if ($dat) {
            return Tool::jsonResp([
                'err' => 0,
                'msg' => '申请提交成功'
            ]);
        } else {
            return Tool::jsonResp([
                'err' => '404',
                'msg' => '申请提交失败'
            ]);
        }
    }

    public function masterVerifyFile(Request $req)
    {
        if(!$req->hasFile('id_card_img')) {
            return false;
        }
        $file = $req->file('id_card_img');

        foreach ($file as $k => $value){
            $fileName = 'skb_'.time().rand(1000, 9999).'.'.$value->getClientOriginalExtension();
            $folder   = 'masterVerify/'.date('Ymd');
            if($value->move('../../merge/public/uploads/'.$folder, $fileName)){
                $path[$k] = $folder.'/'.$fileName;
            }
        }
        return $path;
    }

    /**
     * 对应字段返回错误信息
     * @param $params
     * @param $rules
     * @return mixed
     */
    public function check($params, $rules)
    {
        $message = array(
            "username"          =>  ":attribute 不能为空",
            "id_number"         =>  ":attribute 不能为空",
            "mobile"            =>  ":attribute 不能为空",
            "work_year"         =>  ":attribute 不能为空",
            "work_area"         =>  ":attribute 不能为空",
            "id_card_img"       =>  ":attribute 不能为空",
            "product_type_id"   =>  ":attribute 不能为空",
            "service_type_id"   =>  ":attribute 不能为空",
            "service_sta_time"  =>  ":attribute 不能为空",
            "service_end_time"  =>  ":attribute 不能为空",
        );

        $attributes = array(
            "username"          =>  "师傅真实姓名",
            "id_number"         =>  "身份证号码",
            "mobile"            =>  "手机号码",
            "work_year"         =>  "工作年限",
            "work_area"         =>  "工作区域",
            "id_card_img"       =>  "身份证照片",
            "product_type_id"   =>  "产品类别",
            "service_type_id"   =>  "服务类别",
            "service_sta_time"  =>  "服务开始时间",
            "service_end_time"  =>  "服务结束时间",
        );

        $validator = Validator::make(
            $params,
            $rules,
            $message,
            $attributes
        );

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
    }
}