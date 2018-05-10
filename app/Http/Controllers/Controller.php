<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController,
    Validator;

class Controller extends BaseController
{
    protected $_msg = 'request not allow';

    protected function responseJson($status, $msg, $data = [])
    {
        if (!is_numeric($status) || !is_string($msg)) {
            throw new \InvalidArgumentException('类型错误');
        }

        if (!empty($data)) {
            $array = [
                'err' => $status,
                'msg' => $msg,
                'dat' => $data
            ];
        } else {
            $array = [
                'err' => $status,
                'msg' => $msg
            ];
        }

        return \App\Traits\Tool::jsonResp($array);
    }

    protected function verifyUserParams($params, $rules)
    {
        if (!is_array($params)
            || empty($params)
            || !is_array($rules)
            || empty($rules)
        ) {
            return false;
        }

        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            $this->_msg = $validator->errors()->first();
            return false;
        }

        return true;
    }

}
