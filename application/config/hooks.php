<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
/**
 * 自定义钩子,定义在$hook数组中
 * 需要在application/config/config.php中开启hook功能;
 * filepath相对于application目录;
 * 注意param可选，且其类型为mix(可为string,也可为array，也可为其它)
 *
 * pre_controller称之为挂钩点，重要的可用值如下：
 *  pre_system；
 *  pre_controller: 在你的控制器调用之前执行，所有的基础类都已加载，路由和安全检查也已经完成
 *  post_controller_constructor: 在你的控制器实例化之后立即执行，控制器的任何方法都还尚未调用
 *  post_controller: 在你的控制器完全运行结束时执行
 *  post_system
 *
 * 可以一个挂钩点执行多个脚本，如下面的post_controller
 *
 */
$hook['pre_controller'] = array(
    'class'    => 'MyHook',
    'function' => 'pre_controller',
    'filename' => 'MyHook.php',
    'filepath' => 'hooks/univ',
    'params' => 'pre'
);

$hook['post_controller'][] = array(
    'class'    => 'MyHook',
    'function' => 'post_controller',
    'filename' => 'MyHook.php',
    'filepath' => 'hooks/univ',
    'params' => array('java', 'php', 'c++')
);
$hook['post_controller'][] = array(
    'class'    => 'MyHook',
    'function' => 'post_controller2',
    'filename' => 'MyHook.php',
    'filepath' => 'hooks/univ'
);

