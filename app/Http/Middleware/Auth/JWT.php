<?php

namespace App\Http\Middleware\Auth;

use Closure;
use App\Traits\Tool;
use Firebase\JWT\JWT as FirebaseJWT;

class JWT
{
    protected $auth = false;

    public function __construct()
    {
        try {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])
                && $jwt = $_SERVER['HTTP_AUTHORIZATION']
            ) {
                $this->auth = (array) FirebaseJWT::decode(
                    $jwt,
                    env('SECRET_KEY'),
                    ['HS256']
                );
            }
        } catch (\Exception $e) {
            $this->auth = $this->authorise();
        } finally {
        }
    }

    public function handle($request, Closure $next)
    {
//        if (false === $this->auth) {
//            return response()->json([
//                'error' => 'Unauthorized'
//            ], 401);
//        }

        if (false === $this->auth) {
            return Tool::jsonResp([
                'err' => 401,
                'msg' => 'Unauthorized',
            ]);
        }

        $request->attributes->add([
            'jwt_auth' => $this->auth,
        ]);

        return $next($request);
    }
}