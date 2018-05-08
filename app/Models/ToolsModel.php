<?php

namespace App\Models;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ToolsModel
{
    // 计算经纬度距离
    public function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        // deg2rad()函数将角度转换为弧度
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin( sqrt ( pow (sin($a / 2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2),2) ) ) * 6378.137 * 1000;
        $s = round($s,2);

        return ($s < 1000) ? ( round($s, 2) . 'm') : round( intval($s / 1000).'.'.( $s % 1000), 2).'km';
    }

    // 计算两点骑行距离
    public function getBicyclingDistanceInAmap($from, $to)
    {
        if (empty(env('AMAP_KEY'))) {
            throw new \Exception('Missing AMAP KEY');
        }

        $key = env('AMAP_KEY');
        $api = <<< EOF
http://restapi.amap.com/v4/direction/bicycling?origin={$from}&destination={$to}&key={$key}
EOF;
        $ret = $this->requestJsonApi($api, 'GET');
        if ($ret = (isset($ret['res']) ? $ret['res'] : false)) {
            if ($ret = (isset($ret['data']['paths'][0])
                ? $ret['data']['paths'][0] : false
            )) {
                if (isset($ret['distance']) && is_numeric($ret['distance'])) {
                    return $ret['distance'];
                }
            }
        }

        return false;
    }
}