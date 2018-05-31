<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\SkbAddress as SkbAddressModel;
use App\Traits\{Tool, Session};
use Illuminate\Http\Request;

class SkbAddress extends Controller
{
    /**
     * 获取地址
     * @param Session $ssn
     * @return $this
     */
    public function getAddress(Session $ssn)
    {
        $uid = $ssn->get('user.id');

        if ($uid && is_int($uid)) {
            $dat = SkbAddressModel::where('uid', $uid)->get();
        }

        $dat = $dat ?? [];
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : 'fail';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat
        ]);
    }

    /**
     * 新增或更新地址
     * @param Request $req
     * @param Session $ssn
     * @return $this
     */
    public function createOrUpdateAddress(Request $req, Session $ssn)
    {
        $uid = $ssn->get('user.id');

        if (!$uid || !is_int($uid)) {
            return Tool::jsonResp([
                'err' => 403,
                'msg' => '参数错误'
            ]);
        }

        $this->validate($req, [
            'contacts' => 'required|string',
            'contacts_mobile' => [
                'required',
                'numeric',
                'regex:/^1[3-9][0-9]{9}$/'
            ],
            'sex'  => 'required|numeric',
            'area' => 'required|string',
            'addr' => 'required|string',
            'tag'  => 'required|numeric',
            'is_default' => 'required|numeric',
            'area_id'    => 'required|numeric'
        ]);

        $params = $req->all();

        $addressModel = new SkbAddressModel;

        if ($params && !isset($params['id'])) {

            $params['created_at'] = date('Y-m-d H:i:s', time());
            $params['updated_at'] = date('Y-m-d H:i:s', time());

            $res = $addressModel->insert($params);
        } else {

            $params['updated_at'] = date('Y-m-d H:i:s', time());

            $res = $addressModel->where('id', $params['id'])->update($params);
        }

        $dat = $res ?? [];
        $err = $res ? 0 : 404;
        $msg = $res ? 'success' : 'fail';

        return Tool::jsonResp([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat
        ]);
    }


    public function delOnceAddress(Request $req)
    {
        $address_id = $req->get('id');

        $addressModel = new SkbAddressModel;

        $res = $addressModel->delOnceAddress($address_id);

        if ($res) {
            return Tool::jsonResp([
                'err' => 0,
                'msg' => '删除成功',
            ]);
        }
    }

    public function delAllAddress()
    {
        // TODO
    }
}