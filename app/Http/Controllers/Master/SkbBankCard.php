<?php

namespace App\Http\Controllers\Master;


use App\Http\Controllers\Controller,
    App\Traits\Tool,
    App\Models\Master\SkbBankCard as SkbBankCardModel;
use Illuminate\Http\Request,
    Illuminate\Support\Facades\Validator;

class SkbBankCard extends Controller
{
    /**
     * 银行卡列表
     * @param Request $req
     * @return $this
     */
    public function bankCardList(Request $req)
    {
        $this->validate($req, [
            'master_id' => 'required|numeric',
        ]);

        $master_id = $req->get('master_id');

        $bankcard = new SkbBankCardModel();

        $dat = $bankcard->getBankCardListById($master_id);
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : 'fails';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat,
        ]);

    }

    /**
     * 添加银行卡
     * @param Request $req
     * @param SkbBankCardModel $bankcard
     * @return $this
     */
    public function createOrUpdateBankCard(Request $req, SkbBankCardModel $bankcard)
    {
        $params = $req->all();

        $rules  =  [
            'real_name'   => 'required|string',
            'ID_number'   => [
                'required',
                'regex:/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$)/',
            ],
            'bank_reserve_mobile' => [
                'required',
                'numeric',
                'regex:/^1[3-9][0-9]{9}$/'
            ],
            'bank_card_number' => [
                'required',
                'regex:/^(\d{16}|\d{19}|\d{17})$/'
            ],
            'bank_name' => 'required|string',
            'bank_branch_name' => 'required|string',
        ];

        if ($msg = $this->check($params, $rules)) {
            return Tool::jsonResp([
                'err' => '403',
                'msg' => $msg,
            ]);
        }

        $params['is_verify'] = -1;
        $params['created_at'] = date('Y-m-d H:i:s', time());
        $params['updated_at'] = date('Y-m-d H:i:s', time());

        $dat = $bankcard->insertBankCard($params);

        if ($dat) {
            return Tool::jsonResp([
                'err' => 0,
                'msg' => '添加成功'
            ]);
        } else {
            return Tool::jsonResp([
                'err' => '404',
                'msg' => '添加失败'
            ]);
        }
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
            "real_name"      => ":attribute 不能为空",
            "id_number"      => ":attribute 不能为空",
            "bank_reserve_mobile"   => ":attribute 不能为空",
            "bank_card_number"      => ":attribute 不能为空",
            "bank_name"      => ":attribute 不能为空",
            "bank_branch_name"      => ":attribute 不能为空",
        );

        $attributes = array(
            "real_name"      => "真实姓名",
            "id_number"      => "身份证号码",
            "bank_reserve_mobile"   => "银行预留手机号",
            "bank_card_number"      => "银行卡号",
            "bank_name"       => "开户银行",
            "bank_branch_name"      => "开户支行",
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