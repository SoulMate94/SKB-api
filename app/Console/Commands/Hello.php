<?php

// Custom artisan commands
// @caoxl

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $name = 'patch:hello';
    protected $description = 'Balabalabala';

    // 如果要支持命令行参数 必须覆盖这个 `$signature`
    // 否则会报错："Too many arguments"
    protected $signature = 'patch:hello {argv}';

    public function fire()
    {
        $this->info('hello artisan cmd');

        // 获取命令行参数
        // php artisan patch:hello '{"a":"b"}'
        dd($this->arguments());

        /*输出数组: [
            'command' => 'patch:hello',
            'argv'    => {"a":"b"}
        ]*/
    }
}