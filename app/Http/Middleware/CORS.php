<?php

namespace App\Http\Middleware;

use Closure;
use Monolog\Handler\RotatingFileHandler;

class CORS
{
    public function __construct()
    {
    }

    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Headers' => 'Access-Control-Allow-Origin', 'AUTHORIZATION',
            'Access-Control-Max-Age'       => 86400,
        ];

        if ('OPTIONS' == $request->getMethod()) {
            return response(null, 200, $headers);
        }

        return $next($request)->withHeaders($headers);
    }

    // TODO
    public function terminate($request, $response)
    {
        $headerRsp = $response->headers->all();
        $ctntType  = $headerRsp['content-type'][0] ?? 'text/plain';
        if (false !== mb_strpos($ctntType, 'application/json')) {
            $path = storage_path().'/api-io/';
            if (! file_exists($path)) {
                @mkdir($path);
            }

            $timestamp = time();

            file_put_contents(
                $path
                .date('Y-m-d', $timestamp).'.log',

                json_encode([
                    'When'          => [
                        'Time'      => date('H:i:m', $timestamp),
                        'Timestamp' => $timestamp,
                        'Timezone'  => date_default_timezone_get(),
                    ],
                    'Request'    => [
                        'path'   => $request->path(),
                        'method' => $request->method(),
                        'Header' => $request->headers->all(),
                        'Data'   => $request->all(),
                    ],
                    'Response'   => [
                        'Header' => $headerRsp,
                        'Status' => $response->status(),
                        'Data'   => $response->content(),
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
                .PHP_EOL
                .PHP_EOL,

                FILE_APPEND | LOCK_EX
            );
        }
    }
}