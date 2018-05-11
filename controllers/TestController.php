<?php
/**
 * Created by PhpStorm.
 * User: sgd
 * Date: 16/7/5
 * Time: 17:24
 */

namespace app\controllers;
use yii\web\Controller;


class TestController extends Controller
{

    public function actionIndex(){
        $request = \Yii::$app->getRequest()->get();
        var_dump('：你在哪呢？'.PHP_EOL);
        var_dump('：在外边'.PHP_EOL);
        \Yii::$app->params;
    }
    /**
     * 借助CURL模拟HTTP接口调用
     *
     * @param  string  method  请求方法
     * @param  string  url     接口地址
     * @param  array   param   请求参数
     * @param  array   header  其他HTTP请求头
     * @param  integer timeout 响应超时时间
     * @return string  response
     */
    public static function curlRequest($method, $url, $param = array(), $header = array(), $timeout = 5)
    {
        $defaults = [
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.122 Safari/537.36 RRX Agent',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout
        ];

        $options = [];

        if (sizeof($header) > 0)
        {
            $options[CURLOPT_HTTPHEADER] = $header;
        }

        if (strtolower($method) == 'post')
        {
            $options[CURLOPT_URL] = $url;
            $options[CURLOPT_POST] = 1;
            if (is_array($param))
            {
                $options[CURLOPT_POSTFIELDS] = http_build_query($param);
            }
            else
            {
                $options[CURLOPT_POSTFIELDS] = $param;
            }
        }
        else
        {
            if (strpos($url, '?') !== False)
            {
                $options[CURLOPT_URL] = $url . '&' . http_build_query($param);
            }
            else
            {
                $options[CURLOPT_URL] = $url . '?' . http_build_query($param);
            }
        }
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        if(!$result = curl_exec($ch))
        {
            $errno = curl_errno($ch);
            throw new \Exception("Curl error [$errno]: " . curl_error($ch));
        }
        curl_close($ch);
        error_log("[".date('Y-m-d H:i:s')."] "."[$url] [".json_encode($param)."]\n",3,"/data/logs/reward_curl.log");
        return $result;
    }

}