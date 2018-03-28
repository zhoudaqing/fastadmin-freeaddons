<?php

namespace addons\clsms\library;

/**
 * 创蓝SMS短信发送
 * 如有问题，请加微信  andiff424  QQ:165607361
 */
class Clsms
{

    private $_params = [];
    protected $error = '';
    protected $config = [];

    public function __construct($options = [])
    {
        if ($config = get_addon_config('clsms'))
        {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Clsms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 立即发送短信
     *
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params();
        $postArr = array(
            'account'  => $params['account'],
            'password' => $params['password'],
            'msg'      => $params['msg'] . ($params['smstype'] ? '退订请回T.' . $params['sign'] : $params['sign']),
            'phone'    => $params['mobile'],
            'report'   => $params['report']
        );
        $options = [
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=utf-8'
            )
        ];
        $result = \fast\Http::sendRequest('http://smssh1.253.com/msg/send/json', json_encode($postArr), 'POST', $options);
        if ($result['ret'])
        {
            $res = (array) json_decode($result['msg'], TRUE);
            if (isset($res['code']) && $res['code'] == '0')
                return TRUE;
            $this->error = isset($res['Message']) ? $res['Message'] : 'InvalidResult';
        }
        else
        {
            $this->error = $result['msg'];
        }
        return FALSE;
    }

    private function _params()
    {
        $smstype = isset($this->_params['smstype']) ? $this->_params['smstype'] : 0;
        return array_merge([
            'smstype'  => $smstype,
            'account'  => ($smstype ? $this->config['key1'] : $this->config['key']),
            'password' => ($smstype ? $this->config['secret1'] : $this->config['secret']),
            'sign'     => $this->config['sign'],
            'report'   => true,
        ], $this->_params);
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 短信类型
     * @param   string    $st       0验证码1会员营销短信（会员营销短信不能测试）
     * @return Clsms
     */
    public function smstype($st = 0)
    {
        $this->_params['smstype'] = $st;
        return $this;
    }

    /**
     * 接收手机
     * @param   string  $mobile     手机号码
     * @return Clsms
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 短信内容
     * @param   string  $msg        短信内容
     * @return Clsms
     */
    public function msg($msg = '')
    {
        $this->_params['msg'] = $msg;
        return $this;
    }

}
