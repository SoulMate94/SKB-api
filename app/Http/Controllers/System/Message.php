<?php

// All messages in API system
// @caoxl

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Traits\Tool;

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
}