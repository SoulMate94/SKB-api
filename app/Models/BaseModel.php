<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BaseModel
{
    static function dbGet($table, $id, $lockForUpdate = false)
    {
        if ($lockForUpdate) {
            return DB::table($table)->where('id', intval($id))->lockForUpdate()->first();
        } else {
            return DB::table($table)->where('id', intval($id))->first();
        }
    }

    static function dbGetWith($table, $field, $param, $lockForUpdate = false)
    {
        if ($lockForUpdate) {
            return DB::table($table)->where($field, $param)->lockForUpdate()->first();
        } else {
            return DB::table($table)->where($field, $param)->first();
        }
    }

    static function dbGetWithWheres($table, $wheres = array(), $lockForUpdate = false)
    {
        $model = DB::table($table);
        $mode  = self::__wheres($model, $wheres);
        if ($lockForUpdate) {
            return $model->lockForUpdate()->first();
        } else {
            return $model->first();
        }
    }

    static function dbGetList($table, $wheres = array(), $orders = array(), $pageSize = 0, $lockForUpdate = false)
    {
        $model = DB::table($table);
        $model = self::__wheres($model, $wheres);
        $model = self::__orders($model, $orders);
        if ($pageSize > 0) {
            $model->paginate($pageSize);
        }
        if ($lockForUpdate) {
            return $model->lockForUpdate()->get();
        } else {
            return $model->get();
        }
    }

    static function dbGetCount($table, $wheres = array(), $lockForUpdate = false)
    {
        $model = DB::table($table);
        $model = self::__wheres($model, $wheres);
        if ($lockForUpdate) {
            return $model->lockForUpdate()->count();
        } else {
            return $model->count();
        }
    }

    static function dbInsert($table, $params = array())
    {
        $params['create_at'] = time();

        return DB::table($table)->insertGetId($params);
    }

    static function dbUpdate($table, $id, $params = array())
    {
        $params['update_at'] = time();

        return DB::table($table)->where('id', $id)->update($params);
    }

    static function dbUpdateWithWheres($table, $wheres = array(), $params = array())
    {
        $params['update_at'] = time();
        $model = DB::table($table);
        $model = self::__wheres($model, $wheres);

        return $model->update($params);
    }

    static function dbIncrement($table, $id, $field, $param)
    {
        $params['update_at'] = time();

        return DB::table($table)->where('id', $id)->increment($field, $param);
    }

    static function dbIncrementWithWheres($table, $field, $param, $wheres = array(), $params = array())
    {
        $params['update_at'] = time();
        $model = DB::table($table);
        $model = self::__wheres($model, $wheres);

        return $model->increment($field, $param, $params);
    }

    static function dbDecrement($table, $id, $field, $param)
    {
        $params['update_at'] = time();

        return DB::table($table)->where('id', $id)->decrement($field, $param, $params);
    }

    static function dbDecrementWithWheres($table, $field, $param, $wheres = array(), $params = array())
    {
        $params['update_at'] = time();
        $model = DB::table($table);
        $model = self::_wheres($model, $wheres);

        return $model->decrement($field, $param, $params);
    }

    static function dbDelete($table, $id)
    {
        return DB::table($table)->where('id', $id)->delete();
    }

    static function dbDeleteWithWheres($table, $wheres = array())
    {
        $model = DB::table($table);
        $model = self::__wheres($model, $wheres);

        return $model->delete();
    }

    static function __orders($model, $orders = array())
    {
        if (empty($orders)) {
            $orders = array('id' => 'DESC');
        }

        foreach ($orders as $key => $item) {
            $model->orderBy($key, $item);
        }

        return $model;
    }

    static function __wheres($model, $wheres = array())
    {
        if (empty($wheres)) {
            $wheres = array();
        }

        foreach ($wheres as $key => $item) {
            if (is_array($item) && count($item) === 2) {
                $model->where($key, $item[0], $item[1]);
            } elseif (is_array($item) && count($item) === 3) {
                $model->where($item[0], $item[1], $item[2]);
            } else {
                $model->where($key, $item);
            }
        }

        return $model;
    }

    static function dbOrderSqlByDesc($nextId = 0, $pageSize = 0, $add = true)
    {
        if ($pageSize === 0) {
            return ($add ? ' AND' : '') . ' 1=1 ORDER BY id DESC';
        }

        if ($nextId) {
            return ($add ? ' AND' : '') . ' id < ' . intval($nextId) . ' ORDER BY id DESC' . intval($pageSize);
        } else {
            return ($add ? ' AND' : '') . ' 1=1 ORDER BY id DESC LIMIT ' . intval($pageSize);
        }
    }
}