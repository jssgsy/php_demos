<?php

/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/31
 * Time: 23:20
 *
 * 参见application/config/hooks.php文件
 */
class MyHook {

    // 被挂载在pre_controller上
    public function pre_controller($var){
        echo 'this is the hook pre_controler, the $var is ' . $var . '<br>';
    }

    /**
     * 被挂载在post_controller上
     * @param $var 配置的参数
     */
    public function post_controller($var){
        echo 'this is the hook post_controler, the $var is: ' . '<br>';
        print_r($var);
        echo '<br>';
    }

    // 被挂载在post_controller上
    public function post_controller2() {
        echo 'this is the hook post_controller2, 没有参数 ' . '<br>';
    }

}