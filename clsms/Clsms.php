<?php

namespace addons\clsms;

use think\Addons;

/**
 * 创蓝短信插件
 */
class Clsms extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 短信发送行为
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsSend(&$params)
    {
        $clsms = new library\Clsms();
        $result = $clsms->smstype(0)
                ->mobile($params['mobile'])
                ->msg("你的短信验证码是：{$params['code']}")
                ->send();
        return $result;
    }

    /**
     * 短信发送通知
     * @param   array   $params
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $clsms = new library\Clsms();
        $result = $clsms->smstype(1)->mobile($params['mobile'])
                ->msg($params['msg'])
                ->send();
        return $result;
    }

    /**
     * 检测验证是否正确
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return TRUE;
    }

}
