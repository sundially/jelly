<?php
/**
 * Created by PhpStorm.
 * User: sunguodong
 * Date: 2018/5/17
 * Time: 下午4:09
 */
namespace app\web;

/**
 * 配置分离管理类
 */
class ConfigManager
{
    /**
     * 通过组合server.ini获取配置，
     * @param $config1
     * @param $fileName
     * @return array
     */
    public static function loadConfig($config1,$fileName){
        $config = $config1;
        if(!file_exists($fileName)){
            \yii::info("ini文件不存在" . $fileName);
            return $config;
        }

        $confArr = parse_ini_file($fileName, true);

        foreach ($confArr as $section => $conf) {
            $arrTmp = explode('.', $section);
            if (count($arrTmp) !== 2) {
                \yii::error('错误的ini配置方式'.$section.'IN '.$fileName);
                continue;
            }

            switch ($arrTmp[0]) {
                case 'params':
                    //检查server.ini中的接口配置是否存在
                    if (!isset($config['params'][$arrTmp[1]])) {
                        \yii::info('config中params不存在'.$arrTmp[1].'配置');
                        continue;
                    }

                    //检查server.ini中的变量名是否与分支中的配置相同
                    foreach ($conf as $key => $value){
                        if(isset($config['params'][$arrTmp[1]][$key])){
                            $config['params'][$arrTmp[1]][$key] = $value;

                        } else{
                            \yii::info('ini中params的'.$arrTmp[1].'配置不存在: ' . $key);
                        }
                    }
//                    \yii::info('使用ini中params配置: ' . $arrTmp[1]);
                    break;
                case 'components':
                    //检查server.ini中的接口配置是否存在
                    if (!isset($config['components'][$arrTmp[1]])) {
                        \yii::info('config中components不存在'.$arrTmp[1].'配置');
                        continue;
                    }
                    //检查server.ini中的变量名是否与分支中的配置相同
                    foreach ($conf as $key => $value){
                        if(isset($config['components'][$arrTmp[1]][$key])){
                            $config['components'][$arrTmp[1]][$key] = $value;
                        } else{
                            \yii::info('ini中components的'.$arrTmp[1].'配置不存在: ' . $key);
                        }
                    }
//                    \yii::info('使用ini中components配置: ' . $arrTmp[1]);
                    break;
            }
        }

        return $config;
    }

}