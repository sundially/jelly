<?php
namespace app\components;

use yii\base\Exception;
use Yii;

/**
 * http请求工具累
 */
class HttpProxy {

    /**
     * @var string curl request get method
     */
    const METHOD_GET = 'get';

    /**
     * @var string curl request post method
     */
    const METHOD_POST = 'post';

    /**
     * 加上重试机制
     *
     * @param $url
     * @param $data
     * @param array $options
     * @return array
     * @throws Exception
     */
    public static function curlWithRetry($url, $data, $options = [])
    {
        $defaultOptions = [
            'key' => null,
            'method' => self::METHOD_POST,
            'timeout' => 2,
            'SIGNNAME' => 'sign',
            'TSNAME' => 'ts',
            'basicAuth' => null,
            'header' => true,
            'offOn' => true,
        ];

        $options = array_merge($defaultOptions, $options);
        $retryTimes = isset($options['retryTimes']) && $options['retryTimes'] > 0 ? $options['retryTimes'] : 3;
        while ($retryTimes > 0) {
            $ret = static::curl($url, $data,
                $options['key'],
                $options['method'],
                $options['timeout'],
                $options['SIGNNAME'],
                $options['TSNAME'],
                $options['basicAuth'],
                $options['header']
            );
            $retryTimes--;
            if ($ret['errno'] == 0 || $retryTimes == 0) {
                return $ret;
            }
        }
        throw new Exception('retryTimes 设置错误');
    }

    /**
     * post/get简单工具函数
     *
     * @param string $url
     * @param array  $data default:[]
     * @param null   $key
     * @param string $method post/get
     * @param int    $timeout
     * @param string $SIGNNAME
     * @param string $TSNAME
     * @param null   $basicAuth
     * @param        $header array 决定post情况下,发送数据的协议头类型; false: multipart/form-data  true:application/x-www-form-urlencoded
     *
     * @return array [errno, msg, data]
     */
    public static function curl($url,
                                $data = [],
                                $key=null,
                                $method = self::METHOD_POST,
                                $timeout=2,
                                $SIGNNAME='sign',
                                $TSNAME='ts',
                                $basicAuth=null,$header = true) {

        if (!empty($key)) {
            if (!empty($data[$SIGNNAME])) {
                Yii::error('已经存在sign字段');
                die();
            }

            unset($data[$SIGNNAME]);
            $data[$TSNAME] = (string)time();
            ksort($data);
            $sign = implode('|', $data).'|'.$key;
            Yii::info($sign, 'md5 before');
            $data[$SIGNNAME] = md5($sign);
        }

        $start = microtime(true)*1000;
        Yii::beginProfile($url, 'curl');
        if ($method === self::METHOD_GET) {
            $url = $url . '?' . http_build_query($data);
        }
        $ch = curl_init($url);
        if ($method === self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if($header){
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }else{

                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 不对ssl协议校验证书合法性
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($timeout>0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }
        // basic认证
        if (!empty($basicAuth)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $basicAuth);
        }

        $ctn = curl_exec($ch);
        $msg = curl_error($ch);
        $errno = curl_errno($ch);// int the error number or 0 (zero) if no error occurred.
        curl_close($ch);

        $cost = round((microtime(true)*1000 - $start)/1000, 3);
        Yii::endProfile($url, 'curl');
        $data = strtr(var_export($data, true), ["\t" => '', "\r" => '', "\n" => '', "    " => '']);
        if (strpos($ctn, '<!DOCTYPE') !== false) {
            $ctn = substr($ctn, 0, 20);
        }

        Yii::info ("msg[curl_request_info], url[{$url}], method[{$method}], params[{$data}], cost[{$cost}], curl_exec_ret[{$ctn}], curl_error[$msg], ",
            SgdConsts::LOG_FOR_CODE_PROCESS.'_curl');

        return [
            'data'  => $ctn,
            'msg'   => empty($msg) ? 'success' :$msg,
            'errno' => intval($errno),
        ];
    }

}