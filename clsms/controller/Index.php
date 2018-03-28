<?php

namespace addons\clsms\controller;

use think\addons\Controller;

class Index extends Controller
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 发送验证码
     */
    public function send()
    {
        $mobile = $this->request->post('mobile');
        $msg = $this->request->post('msg');
        $smsd = new \addons\clsms\library\Clsms();
        $ret = $smsd->mobile($mobile)->msg($msg)->send();
        if ($ret)
        {
            $this->success("发送成功");
        }
        else
        {
            $this->error("发送失败！失败原因：" . $smsd->getError());
        }
    }

}
