<?php

namespace app\components;

use Yii;
use yii\helpers\FileHelper;
use yii\log\FileTarget;


/**
 * 后台脚本的LogTarget
 * 主要解决将不同的脚本内容输出到不同的文件中去
 */
class ConsoleFileTarget extends FileTarget
{
    /**
     * @var string 日志文件目录
     */
    public $logPath;
    /**
     * @var string 日志文件后缀
     */
    public $logFilePostfixCallback;

    /**
     * Initializes the route.
     * This method is invoked after the route is created by the route manager.
     */
    public function init()
    {
        parent::init();
        $this->getLogfile();
    }

    /**
     * 获取日志文件名
     *
     * @throws \yii\base\Exception
     */
    private function getLogfile()
    {
        $this->logPath = Yii::getAlias($this->logPath);
        if (!is_dir($this->logPath)) {
            FileHelper::createDirectory($this->logPath, $this->dirMode, true);
        }

        // 获取日志文件名
        if (isset($_SERVER['argv'][1]) && !empty($_SERVER['argv'][1])) {
            $filename = str_replace('/', '-', $_SERVER['argv'][1]);
        } else {
            $filename = 'log';
        }

        if (is_callable($this->logFilePostfixCallback)) {
            $postfix = call_user_func($this->logFilePostfixCallback);
        } else {
            $postfix = date('H') . '.log';
        }

        $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $filename . $postfix;
    }

    public function export()
    {
        $this->getLogfile();
        parent::export();
    }

}