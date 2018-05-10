<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;

class BaseController extends Controller
{
    private $phpInput;
    private $phpInputJson;
    private $phpInputStatus = false;

    public function __construct()
    {
        if ($_ENV['params']['apiAllowCrossDomain']) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, Range, Origin, x-requested-with');
            header('Access-Control-Request-Methods: POST,GET,PUT,DELETE,OPTIONS');
            header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE,OPTIONS');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Expose-Headers: Content-Range');
            header('P3P: CP=CAO PSA OUR');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $_GET['page'] = $this->request('pageNumber', 0) + 1;
        Log::info('request_GET:' . $_GET['page']);
    }

    protected function echoReturn($return)
    {
        $result = json_encode($return);
        Log::info('echoReturn:' . $result);

        echo $result;
    }

    protected function echoSuccess($data = array())
    {
        $result = json_encode(array(
            'code' => 0,
            'data' => $data
        ));

        Log::info('echoSuccess: ' . $result);

        echo $result;
    }

    protected function echoError($code, $message)
    {
        $result = json_encode(array(
            'code'    => $code,
            'message' => $message
        ));

        Log::info('echoError: ' . $result);

        echo $result;
    }

    protected function post($key, $default = '')
    {
        if (isset($_POST[$key])) {
            return $this->formatValueWithDefaultType($_POST[$key], $default);
        } else {
            return $default;
        }
    }

    protected function get($key, $default = '')
    {
        if (isset($_GET[$key])) {
            return $this->formatValueWithDefaultType($_GET[$key], $default);
        } else {
            return $default;
        }
    }

    protected function json($key, $default = '')
    {
        if ($this->phpInputStatus === false) {
            $this->phpInput = file_get_contents("php://input");
            $this->phpInputJson = json_decode($this->phpInput);
            $this->phpInputStatus = true;

            Log::info('php://input: ' . (strlen($this->phpInput) > 20000 ? '(md5)' . md5($this->phpInput) : $this->phpInput));
        }

        if ($default === 'isset-check') {
            return isset($this->phpInputJson->$key);
        }

        if (isset($this->phpInputJson->$key)) {
            return $this->formateValueWithDefaultType($this->phpInputJson->$key, $default);
        } else {
            return $default;
        }
    }

    protected function request($key, $default = '')
    {
        if ($_ENV['params']['apiRequestOnlyJson'] || $this->json($key, 'isset-check')) {
            $value = $this->json($key, $default);
        } else {
            if (isset($_REQUEST[$key])) {
                $value = $this->formatValueWithDefaultType($_REQUEST[$key], $default);
            } else {
                $value = $default;
            }
        }

        if (is_array($value) || is_object($value)) {
            Log::info('request: ' . $key . '/' . json_encode($value));
        } else {
            Log::info('request: ' . $key . '/' . (strlen($value) > 10000 ? '(md5)' . md5($value) : $value));
        }

        return $value;
    }

    protected function getRequestUri()
    {
        $requestUri = '';

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $requestUri = $_SERVER['REDIRECT_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $requestUri = $_SERVER['ORIG_PATH_INFO'];

            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        $requestUri = explode('?', $requestUri);

        return $requestUri[0];
    }

    private function formatValueWithDefaultType($value, $default)
    {
        if (is_integer($default)) {
            return intval($value);
        } elseif (is_float($default)) {
            return floatval($value);
        } else {
            return $value;
        }
    }
}