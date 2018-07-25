<?php

// All messages in API system
// @caoxl

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Traits\{Tool, CURL, Session};
use App\Traits\WeChat\{WeChatToken, FormId};

class Message implements \ArrayAccess
{
    public $lang = null;
    public $text = [];

    protected function path()
    {
        return resource_path().'/sys_msg/'.$this->lang;
    }

    public function get(Request $req)
    {
        $this->lang = $req->get('lang') ?? $this->getDefaultLang();

        return Tool::jsonResp([
            'err' => 0,
            'msg' => 'ok',
            'dat' => $this->load(),
        ]);
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
        return isset($this->text[$offset]);
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
        return isset($this->text[$offset])
            ? $this->text[$offset]
            : (
                ('zh' == $this->lang)
                ? '服务繁忙,请稍后再试'
                : 'The service is busy. Please try again later.'
            );
    }

    public function offsetSet($offset, $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset): void
    {
        // TODO: Implement offsetUnset() method.
    }

    public function msg($lang): self
    {
        $this->load($lang);

        return $this;
    }

    public function load($lang = null)
    {
        if (! $this->lang) {
            $this->lang = $lang ?? $this->getDefaultLang();
        }

        $dat = [];

        if ($fsi = $this->getFilesystemIterator()) {
            foreach ($fsi as $file) {
                if ($file->isFile() && 'php' == $file->getExtension()) {
                    $_dat = include_once $file->getPathname();
                    if ($_dat && is_array($_dat)) {
                        $dat = array_merge($_dat, $dat);
                    }
                }
            }
        }

        return $this->text = $dat;
    }

    protected function getDefaultLang(): string
    {
        return 'zh';
    }

    protected function getFilesystemIterator()
    {
        if (($path = $this->path())) {
            if (! file_exists($path)) {
                $this->lang = 'zh';
                $path = $this->path();
                if (! file_exists($path)) {
                    return false;
                }
            }

            return new \FilesystemIterator($path);
        }

        return false;
    }

    protected function push($opid, $page, $temid, $dat)
    {
        $token  = new WeChatToken();
        $token  = $token->getToken();

        $fmid   = FormId::getFormId($opid);

        $url    = config('service_url.wechat.template_message.send_template_message').$token;

        $data    = [];
        foreach ($dat as $k => $v){
            $data['keyword'.($k+1)]['value'] = $v;
        }

        $vars   = [
            'touser'      => $opid,
            'template_id' => $temid,
            'page'        => $page,
            'form_id'     => $fmid,
            'data'        => $data,
        ];

        $curl   = new CURL();
        $res    = $curl->curlPostSsl($url, json_encode($vars));

        if ($res) {
            return Tool::jsonR(0, 'success', [$res,$url]);
        }

        return Tool::jsonR(-1, 'failed', null);
    }

    public function test(Session $ssn)
    {
        $user   = $ssn->get('user');

        $opid   = $user['openid'];
        $dat    = [
            '123456',
            '30元',
            '2016年8月8日',
            '梨子',
            '123456789',
            '2104-12-09 16:00',
            '200元',
            '20161031162645020777',
        ];

        $temp_id= 'uWnGobxPZ2lqFgpWvAI_ZrFpYFJZkYDEsqVGb86I_oU';

        return $this->push($opid, 'index', $temp_id, $dat);
    }

    public function checkFormId(Session $ssn, FormId $formId)
    {
        $user = $ssn->get('user');
        $res  = $formId->checkFormId($user['openid']);

        if($res){
            return Tool::jsonR(-2, 'we need form_id', $res);
        }

        return Tool::jsonR(0, 'form_id enough', null);
    }

    public function storageFormId(Request $req, FormId $formId, Session $ssn)
    {
        $res  = $formId->storageFormId(json_decode($req->post('form_id'), true));
        
        return Tool::jsonR(-1, 'test', $res);
    }
}