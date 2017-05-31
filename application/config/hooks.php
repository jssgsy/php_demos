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
 */
$hook['pre_controller'] = array(
    'class'    => 'MyHook',
    'function' => 'pre_controller',
    'filename' => 'MyHook.php',
    'filepath' => 'hooks/univ',
    'params' => 'pre'
);

$hook['post_controller'] = array(
    'class'    => 'MyHook',
    'function' => 'post_controller',
    'filename' => 'MyHook.php',
    'filepath' => 'hooks/univ',
    'params' => array('java', 'php', 'c++')
);

