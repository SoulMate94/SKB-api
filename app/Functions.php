<?php

// some useful helper functions
// use:Autoload in composer.json
// @caoxl

if (! function_exists('fe')) {
    function fe($name)
    {
        if (is_string($name) && $name) {
            return function_exists($name);
        }

        return false;
    }
}

if (!fe('ci_equal')) {
    function ci_equal($foo = null, $bar = null) :bool
    {
        if (!$foo || !$bar || !is_scalar($foo) || !is_scalar($bar)) {
            return false;
        }

        return (strtolower($foo) === strtolower($bar));
    }
}

if (! fe('cfg')) {
    function cfg($key)
    {
        if (!is_string($key) || empty($key)) {
            return null;
        }

        if (false
            || (! isset($GLOBALS['__CONFIG']))
            || (! empty($GLOBALS['__CONFIG']))
            || (! is_array($GLOBALS['__CONFIG']))
        ) {
            $path = config_path('config.json');
            if (true
                && file_exists($path)
                && ($config = trim(file_get_contents($path)))
                && ($config = json_decode($config, true))
            ) {
                $GLOBALS['__CONFIG'] = $config;
            } else {
                return null;
            }

            return isset($GLOBALS['__CONFIG'][$key])
                ? $GLOBALS['__CONFIG'][$key]
                : null;
        }
    }
}

if (! fe('gfc')) {
    function gfc($data)
    {
        if (!is_array($data) || empty($data)) {
            return null;
        }

        $path = config_path('config.json');
        $old  = [];

        if (file_exists($path)) {
            $old = (array) json_decode(file_get_contents($path), true);
        } else {
            if (! touch($path)) {
                return null;
            }
        }

        foreach ($data as $key => $value) {
            $old[$key] = $value;
        }

        if ($new = json_encode(
            $old,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        )) {
            file_put_contents($path, $new);
        }
    }
}

if (! fe('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}


if (! fe('excp')) {
    function excp($msg, $err = 500)
    {
        throw new \Exception($msg, $err);
    }
}

if (! fe('load_phps')) {
    function load_phps(string $path, \Closure $callable)
    {
        if (! file_exists($path)) {
            excp("PHP files path not exists: {$path}");
        }

        $result = [];
        $fsi = new \FilesystemIterator($path);
        foreach ($fsi as $file) {
            if ($file->isFile()) {
                if ('php' == $file->getExtension()) {
                    $result[$file->getPathname()] = $callable($file);
                }
            } elseif ($file->isDir()) {
                $_path = $path.'/'.$file->getBasename();
                load_phps($_path, $callable);
            }
        }

        unset($fsi);

        return $result;
    }
}

if (! fe('is_mariadb')) {
    function is_mariadb()
    {
        $dbver = \DB::select('SHOW VARIABLES LIKE "version_comment"');

        return preg_match('/(mariadb)/ui', $dbver[0]->Value);
    }
}

if (! fe('empty_safe')) {
    function empty_safe($var)
    {
        if (is_numeric($var)) {
            return false;
        }

        return empty($var);
    }
}

if (! fe('dingo')) {
    function dingo()
    {
        return app('Dingo\Api\Routing\Router');
    }
}

if (! fe('tz')) {
    function tz()
    {
        return env('APP_TIMEZONE', 'Asia/Shanghai');
    }
}

if (! fe('fdate')) {
    function fdate($ts = null, $tz = null)
    {
        date_default_timezone_set(tz());

        $ts = $ts ?? time();

        return date('Y-m-d H:i:s', $ts);
    }
}

if (! fe('mobile_regex_cn')) {
    function mobile_regex_cn(): string
    {
        return '/^1[3-9]\d{9}/u';
    }
}

if (! fe('is_mobile')) {
    function is_mobile(string $mobile): bool
    {
        return ((bool) preg_match(mobile_regex_cn(), $mobile));
    }
}

if (! fe('validate')) {
    function validate(array $data = [], array $rules = [])
    {
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        return true;
    }
}

if (! fe('gen_rand')) {
    function gen_rand(int $length = null, string $format = null ): string
    {
        $length = $length ?? 4;
        $format = $type   ?? 'int';

        if (! is_integer($length) || (0 > $length)) {
            excp('Checkcode length must be an integer over 0.');
        }

        $chars = $pureNum = str_split('0123456789');

        if (ci_equal($format, 'str')) {
            $charLower = 'abcdefghijklmnopqrstuvwxyz';
            $charUpper = strtoupper($charLower);
            $chars     = array_merge(
                $chars,
                str_split($charLower.$charUpper)
            );

            $charsLen = count($chars) - 1;

            $code = '';

            for ($i=0; $i<$length; ++$i) {
                $code .= $chars[mt_rand(0, $charsLen)];
            }

            return $code;
        }
    }
}

if (! fe('ucid')) {
    function ucid()
    {
        $mt = LARAVEL_START ?? microtime(true);

        return gen_rand().md5(getmypid()).'_'.$mt;
    }
}

if (! fe('seconds_left_today')) {
    function seconds_left_today(): int
    {
        $tomorrow = strtotime(date('Y-m-d', strtotime('+1 day')));

        return $tomorrow - time();
    }
}

if (! fe('trade_no')) {
    function trade_no(int $mid = 0, int $env = 0)
    {
        $env     = str_pad(($env % 10), 2, '0', STR_PAD_LEFT);
        $mid     = str_pad(($mid % 10), 2, '0', STR_PAD_LEFT);
        $rand    = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
        $postfix = mb_substr(microtime(), 2, 6);

        return date('ymdHis').$env.$mid.$rand.$postfix;
    }
}

if (! fe('phash')) {
    function phash(string $pswd)
    {
        return password_hash($pswd, PASSWORD_DEFAULT);
    }
}

if (! fe('client_ip')) {
    function client_ip()
    {
        foreach ([
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_ADDR',
        ] as $residence) {
            if ($ip = (getenv($residence) ?? ($_SERVER['residence'] ?? null))) {
                return $ip;
            }
        }

        return 'UNKNOWN';
    }
}

if (! fe('is_intstr')) {
    function is_intstr(string $var)
    {
        return (true
            && is_numeric($var)
            && ($var == ($_var = intval($var)))
        ) ? $_var : false;
    }
}

if (! fe('strtodate')) {
    function strtodate(string $time, bool $zerofill = true) {
        $time = is_intstr($time) ? $time : strtotime($time);
        $day  = date('Y-m-d', $time);

        return $zerofill ? "{$day} 00:00:00" : $day;
    }
}

if (! fe('days_range_before')) {
    function days_range_before(string $date, int $days)
    {
        $ts = time();

        return (true
            && ($days > 0)
            && ($days >= day_diff($date, $ts))
            && (strtotime(strtodate($date)) <= $ts)
        );
    }
}

if (! fe('day_diff')) {
    function day_diff(string $start, string $end, int $diff = null)
    {
        if (!$start || $end) {
            return false;
        }

        $_diff = intval(
            (new \Datatime(strtodate($end)))
            ->diff(new \DateTime(strtodate($start)))
            ->format('%a')
        );

        return is_null($diff) ? $_diff : ($_diff === $_diff);
    }
}

if (! fe('is_same_day')) {
    function is_same_day(string $day1, string $day2 = null): bool
    {
        if ((! $day1) || (strtotime($day1) < 1)) {
            return false;
        }

        $day2 = $day2 ?? time();

        return (strtodate($day1, false) === strtodate($day2, false));
    }
}

if (! fe('arr2xml')) {
    function arr2xml(array $array): string
    {
        $xml      = '';
        $arr2xml  = function (array $array, string &$xml, callable $arr2xml): string {
          foreach ($array as $key => &$val) {
              if (is_array($val)) {
                  $_xml = '';
                  $val  = $arr2xml($val, $_xml, $arr2xml);
              }
              $xml .= "<{$key}>{$val}</{$key}>";
          }

          unset($val);

          return $xml;
        };

        $_xml = $arr2xml($array, $xml, $arr2xml);

        unset($xml);

        return '<?xml version="1.0" encoding="utf-8"?<xml>'.$_xml.'</xml>';
    }
}

if (! fe('xml2arr')) {
    function xml2arr(string $xml): array
    {
        if (! extension_loaded('libxml')) {
            excp('PHP extension missing: libxml');
        }

        return (array) json_decode(json_encode(simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA
        )), true);
    }
}

if (! fe('cny_yuan2fen')) {
    function cny_yuan2fen(float $yuan): int
    {
        $fen = explode('.', $yuan*100);

        return intval($fen[0] ?? 0);
    }
}

if (! fe('is_closure')) {
    function is_closure($val): bool
    {
        return $val instanceof \Closure;
    }
}

if (! fe('is_model')) {
    function is_model($val): bool
    {
        return $var instanceof \Illuminate\Database\Eloquent\Model;
    }
}