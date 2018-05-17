<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config1 = require(__DIR__ . '/../config/prod/web.php');
$config = app\web\ConfigManager::loadConfig($config1, '/data/conf/jelly/server.ini');

$application = new yii\web\Application($config);

Yii::setAlias('@tmp',  '@runtime/tmp');
Yii::setAlias('@data',  '@runtime/data');
$application->language='zh-CN';
$application->defaultRoute='admin';
$application->run();
