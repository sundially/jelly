<?php
namespace app\components;

use Yii;
use yii\helpers\Json;
use yii\web\ErrorHandler;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 异常处理
 */
class SgdErrorHandler extends ErrorHandler
{
    /**
     * @var string 生产环境错误提示文案
     */
    public $prodMessage = '系统错误，请稍后再试。';

    /**
     * Renders the exception.
     *
     * @param \Exception $exception the exception to be rendered.
     * @return mixed|string
     */
    protected function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            $response->isSent = false;
        } else {
            $response = new Response();
        }
        $response->format = Response::FORMAT_JSON;

        // 获取异常输出
        $errorHandle = Yii::$app->errorHandler;
        if (!empty($errorHandle->exception)) {
//            $html = $errorHandle->renderFile($errorHandle->exceptionView, [
//                'exception' => $errorHandle->exception,
//            ]);
            $message = $errorHandle->exception->getMessage();
            Yii::error('Exception ' .  $message);
        } else {
            $html = '服务出现错误：' . php_uname('a');
            Yii::error('Exception ' .  $html);
            $message = $html;
        }

        if ($errorHandle->exception instanceof NotFoundHttpException) {
            return SgdResponse::error('您来错地方了');
        }

        // 异步发送报警邮件
        static::sendAsycEmail($message);

        // 线上错误文案
        if(EnvUtil::isProdEvn()){
            $message = $this->prodMessage;
        }
        $response->data = SgdResponse::error($message);

        $response->send();
    }


    /**
     * 发送异步邮件
     *
     * @param $message
     */
    public static function sendAsycEmail($message)
    {
        $data = [
            'ip' =>   $_SERVER['SERVER_ADDR'],
            'html' => $message,
            'system' => 'jelly',
            'time' => SgdTime::now(),
        ];
        SgdRedis::executeCommand(Yii::$app->sgdRedis, 'LPUSH', ['', Json::encode($data)]);
    }
}