<?php

/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/31
 * Time: 22:13
 */

/**
 * Class ConfigController
 * 测试自定义配置文件
 * 获取配置项的方法：$this->config->item(配置项名)
 *
 */
class ConfigController extends CI_Controller {

    public function index(){
        //加载application/conifg目录下的配置文件：univ/myconfig.php文件
        $this->load->config('univ/myconfig');
        echo '自定义的配置项univ_name的值为： ' . $this->config->item('univ_name');
    }

}