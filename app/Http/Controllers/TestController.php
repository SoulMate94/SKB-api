<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Jobs\TestJob;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Models\User;

class TestController extends Controller
{

    public function redis()
    {
        Redis::setex('test_redis', 10, 'Lumen Redis Test');

        return Redis::get('test_redis');
    }

    public function queue()
    {
        // $this->dispatch(new TestJob);
        // app('redis')->set('Lumen', 'Hello Lumen.');
        // return app('redis')->get('Lumen');

        $queueId = $this->dispatch(new TestJob('key_'.str_random(4), str_random(10)));

        dd($queueId);
    }

    public function jwt($params = [])
    {
        // JWT = header.payload.signature

        $header = base64_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'SHA256',
        ]));

        $timestamp = time();

        $claims = [
            'exp' => $timestamp+604800,
            'nbf' => $timestamp,
            'iat' => $timestamp,
        ];

        $payload = base64_encode(json_encode(array_merge(
            $params,
            $claims
        )));

        $signature = base64_encode(hash_hmac(
            'sha256',
            $header.'.'.$payload,
            $this->getSkbSecureKey()
        ));

        return implode('.', [$header, $payload, $signature]);
        // eyJ0eXAiOiJKV1QiLCJhbGciOiJTSEEyNTYifQ==.eyJleHAiOjE1MjU0MTk4MTgsIm5iZiI6MTUyNDgxNTAxOCwiaWF0IjoxNTI0ODE1MDE4fQ==.NTU2NmYzYTk3YWM4YzQ4OTdmMjUyYmRkZjAzYTYxM2QwYmQxYmFiN2ZiNGJmOTNlZTJiZTJlYmJmYTQ2ZGU4ZA==
    }

    public function getSkbSecureKey()
    {
        $sk = (!($_sk = env('SKB_JWT_SK')) || !is_string($_sk))
            ? '' : $_sk;

        return $sk;
    }


    public function checkJwt(Request $req)
    {
        $jwt = $req->jwt;

        $jwtComponents = explode('.', $jwt);

        if (3 != count($jwtComponents)) {
            return false;
        }

        list($header, $payload, $signature) = $jwtComponents;

        if ($headerArr = json_decode(base64_decode($header), true)) {
            // dd($headerArr);
            if (is_array($headerArr) && isset($headerArr['alg'])) {
                $alg = strtolower($headerArr['alg']);
                // dd(hash_algos());
                if (in_array($alg, hash_algos())) {
                    if (base64_decode($signature) === hash_hmac(
                        $alg,
                        $header.'.'.$payload,
                        $this->getSkbSecureKey())
                    ) {
                        $data = json_decode(base64_decode($payload), true);
                        // dd($data);
                        // Missing expire date or wrong JWT
                        if (! isset($data['exp'])
                            && !$this->isTimestamp($data['exp'])
                        ) {
                            return false;
                        }

                        // JWT expired
                        // !!! Make sure equal timezone were used both in JWT issuing and JWT checking
                        if (time() > $data['exp']) {
                            return false;
                        }

                        return $data;
                    }
                }
            }
        }

        return false;
    }

    public function isTimestamp($timestamp): bool
    {
        return (
            ($timestamp == intval($timestamp))
            && ($timestamp >= -2147483649)
            && ($timestamp <= 2147483649)
        );
    }
}