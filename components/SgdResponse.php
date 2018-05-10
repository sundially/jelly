<?php

namespace app\components;
use Yii;

/**
 * 提供标准的输出格式
 */
class SgdResponse {

    /**
     * 输出错误信息
     */
    public static function error($errmsg, $errno = SgdConsts::INFO_ERROR, $data = [])
    {
        $ret = [
            'errno' => $errno,
            'errmsg' => $errmsg,
        ];

        if (is_array($data) && !empty($data)) {
            $ret = array_merge($ret, $data);
        }

        Yii::error('ResponseError ' . $errmsg);
        return self::output($ret);
    }

    /**
     * 输出成功信息
     *
     * @param array  $data
     * @param int    $errno
     * @param string $errmsg
     *
     * @return mixed
     */
    public static function ok($data = [], $errno = SgdConsts::INFO_OK, $errmsg = 'Ok.')
    {
        $ret = [
            'errno' => $errno,
            'errmsg' => $errmsg,
        ];

        if (is_array($data)) {
            $ret = array_merge($ret, $data);
        }

        return self::output($ret);
    }

    /**
     * 输出信息
     * @param $data
     * @return mixed
     */
    public static function output($data)
    {
        Yii::info($data);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

}