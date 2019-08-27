<?php

namespace frambo\hawkeye;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class HawkeyeExceptionHandler extends ExceptionHandler{

    /**
     * 接受项目信息
     * @param Exception $exception
     */
    public function report(Exception $exception)
    {
        self::sendLogMessageToHawkeye($exception);
        parent::report($exception);
    }

    /**
     * 发送至鹰眼
     * @param Exception $exception
     */
    public static function sendLogMessageToHawkeye(Exception $exception)
    {
        $config = config('hawkeye');
        $message = "错误信息:".$exception->getMessage()."\n";
        $message.= "错误文件:".$exception->getFile()."\n";
        $message.= "错误行号:".$exception->getLine()."\n";
        $token = self::auth($config['access_key'], $config['access_secret']);
        if (!empty($token)) {
            self::send($token, $config['monitoring_name'], $message);
        }
    }

    /**
     * 鹰眼授权接口
     * @param $key
     * @param $secret
     * @return string
     */
    public static function auth($key, $secret)
    {
        $url = "http://zxxb-hawkeyes.mobby.cn/report/auth";
        $postData = [
            'access_key'    => $key,
            'access_secret' => $secret,
        ];
        $res = self::curl_req($url, $postData);
        if (!empty($res)) {
            $res = json_decode($res, true);
            if ($res['code'] == 0) {
                return $res['data'];
            }
        }
        return '';
    }

    /**
     * 鹰眼发送预警接口
     * @param $token
     * @param $monitoringName
     * @param $notifyMsg
     */
    public static function send($token, $monitoringName, $notifyMsg)
    {
        $url = "http://zxxb-hawkeyes.mobby.cn/report/up";
        $postData = [
            'token' => $token,
            'monitoring_name' => $monitoringName,
            'notify_msg' => $notifyMsg
        ];
        self::curl_req($url, $postData);
    }

    /**
     * curl
     * @param $url
     * @param array $post_data
     * @param array $header
     * @return mixed
     */
    public static function curl_req($url, $post_data=[], $header=[])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        if ($post_data){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        if ($header) {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
