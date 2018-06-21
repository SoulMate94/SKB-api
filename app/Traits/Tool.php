<?php

// Tools, only static methods

namespace App\Traits;

class Tool
{
    public static function dayDiff(string $start, string $end, int $diff)
    {
        if (!$start || $end) {
            return false;
        }

        $start = self::strtodate($start);
        $end   = self::strtodate($end);

        return ($diff == (
            (new \Datetime($end))->diff(new \DateTime($start))->format('%a')
        ));
    }

    public static function isSameDay(string $day1, string $day2)
    {
        if (!$day1 || !$day2) {
            return false;
        }

        return (self::strtodate($day1) === self::strtodate($day2));
    }

    public static function strtodate($day)
    {
        if (! is_numeric($day)) {
            $day = strtotime($date('Y-m-d', strtotime($day)));
        }

        return date('Y-m-d H:i:s', strtotime(date('Y-m-d', $day)));
    }

    public static function sysMsg(string $key, $lang = 'zh')
    {
        $lang = $_REQEST['lang'] ?? 'zh';

        if (isset($GLOBALS['__sys_msg'])
            && is_array($GLOBALS['__sys_msg'])
            && $GLOBALS['__sys_msg']
        ) {
            $msg = $GLOBALS['__sys_msg'];
        } else {
            $msg = [];
            $langPath = resource_path().'/sys_msg';
            $path = $langPath.$lang;
            if (! file_exists($path)) {
                $path = $langPath.'zh';
            }

            if (file_exists($path)) {
                $fsi = new \FilesystemIterator($path);
                foreach ($fsi as $file) {
                    if ($file->isFile() && 'php' == $file->getExtension()) {
                        $_msg = include $file->getPathname();
                        if ($_msg && is_array($_msg)) {
                            $msg = array_merge($_msg, $msg);
                        }
                    }
                }

                $GLOBALS['__sys_msg'] = $msg;
            }
        }

        return $msg[$key] ?? strtoupper($key);
    }

    public static function xmlToArray(string $xml)
    {
        return json_decode(json_encode(simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA
        )), true);
    }

    public static function array2XML(array $array, string &$xml): string
    {
        foreach ($array as $key => &$val) {
            if (is_array($val)) {
                $_xml = '';
                $val = self::array2XML($val, $_xml);
            }
            $xml .= "<$key>$val</$key>";
        }

        unset($val);

        return $xml;
    }

    public static function isTimestamp($timestamp): bool
    {
        return (
            is_integer($timestamp)
            && ($timestamp >= 0)
            && ($timestamp <= 2147472000)
        );
    }

    public static function jsonResp(
        $data,
        int $status = 200,
        bool $unicode = true
    ) {
        $unicode = $unicode ? JSON_UNESCAPED_UNICODE : null;

        $data = json_encode($data, $unicode);

        return response($data)
            ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public static function jr(
        $err,
        $msg,
        $dat,
        int $status = 200,
        bool $unicode = true
    ) {
        $unicode = $unicode ? JSON_UNESCAPED_UNICODE : null;

        $data = json_encode([
            'err' => $err,
            'msg' => $msg,
            'dat' => $dat
        ], $unicode);

        return response($data)
            ->header('Content-Type', 'application/json; charset=utf-8');
    }

}