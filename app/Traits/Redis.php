<?php

// Redis Reference Resources
// 这个文件仅供忘记Redis命令时参考,使用Redis无需重新封装.
// @caoxl

namespace App\Traits;

class Redis
{
    public function set($key, $val)
    {
        Redis::set($key, $val);
    }

    public function setex($key, $exp, $val)
    {
        Redis::setex($key, $exp, $val);
    }

    public function get($key)
    {
        return Redis::get($key);
    }

    public function expire($key, $exp = 180)
    {
        Redis::expire($key, $exp);
    }

    public function incr($key)
    {
        Redis::incr($key);
    }

    public function del($key)
    {
        Redis::del($key);
    }

    public function exists($key)
    {
        return Redis::exists($key);
    }
}