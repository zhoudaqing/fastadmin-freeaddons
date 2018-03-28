<?php

namespace addons\adminlte\controller;

use think\addons\Controller;

/**
 * 二维码生成
 *
 */
class Index extends Controller
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    // 
    public function index()
    {
        return $this->view->fetch();
    }

}
