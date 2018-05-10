<?php

namespace app\components;

use Yii;
use yii\base\Application;
use yii\base\Component;

/**
 * 性能分析器
 *
 * @Property \yii\log\Logger $logger
 *
 */
class Profiler extends Component
{
    /**
     * 日志种类
     */
    public $category = '\\app\\components\\Profiler';

    /**
     *  是否输出数据库的sql性能
     */
    public $dbProfiling = false;

    public $prefix = 'profiler';

    public $costSeparator = ' --cost-- ';

    /**
     * 初始化
     */
    public function init()
    {
        Yii::$app->on(Application::EVENT_AFTER_REQUEST, [$this, 'output']);
    }

    /**
     * 获取日志
     *
     * @return \yii\log\Logger
     */
    public function getLogger()
    {
        return Yii::$app->log->logger;
    }

    /**
     * 输出性能结果
     */
    public function output()
    {
        Yii::info($this->getProfile(), 'ProfilingResult');
    }

    /**
     * 性能统计
     *
     * @return array
     */
    private function getProfile()
    {
        $timings = $this->getLogger()->getProfiling([$this->category]);

        $ret = [];

        foreach ($timings as $timing) {
            $ret[] = $this->getProfileItem($timing);
        }

        if ($this->dbProfiling) {
            $dbTimings = $this->getLogger()->getProfiling(['yii\db\Command::query', 'yii\db\Command::execute']);
            foreach ($dbTimings as $timing) {
                $ret[] = $this->getProfileItem($timing);
            }
        }

        return $ret;
    }

    /**
     * 生成日志条目
     *
     * @param $timing array
     * @return string
     */
    private function getProfileItem($timing)
    {
        return $this->prefix . '-[info:' .$timing['info'] . ']' . $this->costSeparator . $timing['duration'] . $this->costSeparator;
    }

    /**
     * 开始计时
     *
     * @param $token
     */
    public function beginProfile($token)
    {
        Yii::beginProfile($token, $this->category);
    }

    /**
     * 结束计时
     *
     * @param $token
     */
    public function endProfile($token)
    {
        Yii::endProfile($token, $this->category);
    }
}