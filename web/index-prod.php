#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/web/ConfigManager.php');

$config1 = require(__DIR__ . '/config/prod/console.php');
$config = app\web\ConfigManager::loadConfig($config1,'/data/conf/aeolus/server.ini');

$application = new yii\console\Application($config);
Yii::setAlias('@data',  '@runtime/data');
$exitCode = $application->run();
exit($exitCode);