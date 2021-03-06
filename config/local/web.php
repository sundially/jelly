<?php
$params = require (__DIR__.'/params.php');
date_default_timezone_set ("PRC");

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(dirname(__DIR__)),
    'bootstrap'  => ['log'],
    'timeZone'  => 'PRC',
    'components' => \yii\helpers\ArrayHelper::merge ([
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'fhsdoifsdjofisdogndfsoi[gnifsndifnsdsdfdsfsdnsdv[',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redisCache'   => [
            'class' => 'yii\redis\Cache',
        ],
        'user'         => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl'        => ['admin/login'],
        ],
        'authManager'  => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler' => [
//            'class' => 'app\components\SgdErrorHandler',
'errorAction' => 'site/error',
        ],

        'mailer'       => [
            'class'     => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtp.exmail.qq.com',
                'username'   => '.com',
                'password'   => '',
                'port'       => '',
                'encryption' => 'ssl',
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'        => 'yii\log\FileTarget',
                    'logFile'      => '/data/logs/'.date ('Ymd').'/'.date ('H').'.log',
                    'levels'       => ['error', 'warning', 'info'],
                    'maxFileSize'  => 2048000,
                    'rotateByCopy' => false,
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'enableStrictParsing' => false,
            'rules' => [
//                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'hbase' => [
            'class' => 'outer\hbase\Connection',
            'host' => '',
            'port' => '20550',
            'connectionTimeout' => 2,
            'dataTimeout'       => 2,
        ],
//        'profiler'   => [
//            'class'       => 'app\components\Profiler',
//            'dbProfiling' => true,
//        ],
    ],
        require (dirname (__DIR__).'/common/web-components.php')),

    'params' => \yii\helpers\ArrayHelper::merge(require(dirname(__DIR__) . '/common/params.php'), $params),
];
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '36.110.61.100'] // 按需调整这里
    ];

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.100.4', '36.110.61.100'] // 按需调整这里
    ];
}
return $config;
