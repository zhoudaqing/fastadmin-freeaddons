<?php

namespace addons\geet\controller;

use addons\geet\library\GeetestLib;
use fast\Random;
use think\addons\Controller;
use think\App;
use think\Config;
use think\Request;

/**
 * 极验证管理
 */
class Index extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    /**
     * 初始化验证码
     */
    public function start()
    {
        $config = get_addon_config('geet');
        if (!$config['CAPTCHA_ID'] || !$config['PRIVATE_KEY']) {
            $this->error('请先在后台中配置极验证的参数信息');
        }
        $GtSdk = new GeetestLib($config['CAPTCHA_ID'], $config['PRIVATE_KEY']);
        $data = array(
            "user_id"     => Random::nozero(6), # 网站用户id
            "client_type" => $this->request->isMobile() ? 'h5' : 'web', #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address"  => $this->request->ip() # 请在此处传输用户请求验证时所携带的IP
        );

        $status = $GtSdk->pre_process($data, 1);
        session('gtserver', $status);
        session('geet_user_id', $data['user_id']);
        $this->success('', null, $GtSdk->get_response());
    }

    /**
     * 校验验证码
     */
    public function check()
    {
        $moduleurl = $this->request->post('geetmoduleurl');
        $module = $this->request->post('geetmodule');
        $url = $this->request->post('geeturl');
        if (!$url) {
            $this->error(__('Invalid parameters'));
        }
        //$url = preg_replace("/\/(\w+)\.php\//i", '/', $url);
        $data = array(
            "user_id"     => session('geet_user_id'), # 网站用户id
            "client_type" => $this->request->isMobile() ? 'h5' : 'web', #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address"  => $this->request->ip() # 请在此处传输用户请求验证时所携带的IP
        );
        $config = get_addon_config('geet');
        if (!$config['CAPTCHA_ID'] || !$config['PRIVATE_KEY']) {
            $this->error('请先在后台中配置极验证的参数信息');
        }
        $GtSdk = new GeetestLib($config['CAPTCHA_ID'], $config['PRIVATE_KEY']);
        if (session('gtserver') == 1) {
            //服务器正常
            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
            if (!$result) {
                $this->error('请先完成验证！');
            }
        } else {
            //服务器宕机,走failback模式
            if (!$GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
                $this->error('请先完成验证！');
            }
        }
        // 设置验证始终返回true
        \think\Validate::extend('captcha', function ($value, $id = "") {
            return true;
        });

        if ($module && $moduleurl && stripos($moduleurl, '.php') !== false) {
            // 绑定到admin模块
            \think\Route::bind($module);
            $url = substr($url, strlen($moduleurl));
        }

        // 重新模拟一次请求
        $request = Request::create($url, $this->request->method(), $this->request->param(), $this->request->cookie(), $this->request->file(), $this->request->server());
        //$request->module('admin');
        App::run($request)->send();
        return;
    }

}
