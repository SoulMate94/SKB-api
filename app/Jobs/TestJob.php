<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class TestJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $key;
    private $value;

    public function __construct($key, $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    public function handle()
    {
        Redis::hset('queue.test', $this->key, $this->value);
    }

    public function failed()
    {
        dump('failed');
    }
}
