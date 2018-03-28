<?php

namespace addons\adminlte;

use think\Addons;

/**
 * AdminLTE插件
 */
class Adminlte extends Addons
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

}
