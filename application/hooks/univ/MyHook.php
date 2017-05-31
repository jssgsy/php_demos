<?php

/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/31
 * Time: 23:20
 */
class MyHook {

    public function pre_controller($var){
        echo 'this is the hook pre_controler, the $var is ' . $var . '<br>';
    }

    public function post_controller($var){
        echo 'this is the hook post_controler, the $var is ';
        print_r($var);
    }

}