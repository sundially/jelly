<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
date_default_timezone_set("PRC");
$params = require(__DIR__ . '/params.php');

return [
    'id'                  => 'basic-console',
    'basePath'            => dirname(dirname(__DIR__)),
    'bootstrap'           => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules'             => [
        'gii'        => 'yii\gii\Module',
    ],
    'components'          => [
        'cache'          => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'            => [
            'flushInterval' => 1,
            'targets'       => [
                [
                    'class'                  => 'app\components\ConsoleFileTarget',
                    'logPath'                => '/data/logs/' . date('Ymd') . '/',
                    'logFilePostfixCallback' => function () {
                        return 'con' . date('H') . '.log';
                    },
                    'logVars'                => [],
                    'levels'                 => ['error', 'warning', 'info'],
                    'maxFileSize'            => '1024000',
                    'exportInterval'         => 1,
                    'rotateByCopy'           => false,
                ],
            ],
        ],
        'authManager'    => [
            'class' => 'yii\rbac\PhpManager',
        ],
        'hbase' => [
            'class' => 'outer\hbase\Connection',
            'host' => '',
            'port' => '20550',
        ],
        'mailer'         => [
            'class'     => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtp.exmail.qq.com',
                'username'   => '.com',
                'password'   => '',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
        ],
        'ftp'            => [
            'class'         => '\gftp\FtpComponent',
            'driverOptions' => [
                'class'   => '\gftp\drivers\FtpDriver',
                'user'    => 'root',
                'pass'    => '1234',
                'host'    => '',
                'port'    => 21,
                'timeout' => 10,
                'passive' => false,
            ],
        ],
    ],
    'params'              => \yii\helpers\ArrayHelper::merge(require(dirname(__DIR__) . '/common/params.php'),
        $params),
];
