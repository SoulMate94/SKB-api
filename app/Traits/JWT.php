<?php

namespace App\Traits;

trait JWT
{
    protected $getSecureKey = 'SKB';

    public function issue($params = [])
    {
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

        $payload   = base64_encode(json_encode(array_merge($params, $claims)));

        $signature = base64_encode(hash_hmac(
            'sha256',
            $header.'.'.$payload,
            $this->getSecureKey
        ));

        return implode('.', [$header, $payload, $signature]);
    }

    public function getSecureKey()
    {
        // TODO
        $sk = (!($_sk = env('SKB_JWT_SK')) || is_string($_sk))
            ? '' : $_sk;

        return $sk;
    }

    public function check($jwt)
    {
        $jwtComponents = explode('.', $jwt);

        if (3 != count($jwtComponents)) {
            return false;
        }

        list($header, $payload, $signature) = $jwtComponents;
        if ($headerArr = json_decode(base64_decode($header), true)) {
            if (is_array($headerArr) && isset($headerArr['alg'])) {
                $alg = strtolower($headerArr['alg']);
                if (in_array($alg, hash_algos())) {
                    if (base64_decode($signature) === hash_hmac(
                        $alg,
                        $header.'.'.$payload,
                            $this->getSecureKey
                        )) {
                        $data = json_decode(base64_decode($payload), true);

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
            && ($timestamp <=  2147483649)
        );
    }

    // Authorise in HTTP HEADER `AUTHORIZATION`
    public function authorise()
    {
        if (! isset($_SERVER['HTTP_AUTHORIZATION'])
            || !is_string($_SERVER['HTTP_AUTHORIZATION'])
        ) {
            return false;
        }

        return $this->check($_SERVER['HTTP_AUTHORIZATION']);
    }

}