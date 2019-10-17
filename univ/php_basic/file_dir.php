<?php
/**
 * 文件、目录相关
 * User: univ <minglu.liu@beibei.com>
 * Date: 2019-10-17 10:45
 * @copyright Beidian Limited. All rights reserved.
 */
echo __FILE__ . '<br>';
var_dump(is_file(__FILE__));
var_dump(is_dir(__FILE__));
echo dirname(__FILE__) . '<br>';
echo basename(__FILE__) . '<br>';

// 获取当前目录
echo getcwd() . '<br>';

// 假如当前目录为.../a,执行下面方法后，当前目录为.../a/rabbitmq
chdir('rabbitmq');
echo getcwd() . '<br>';


mkdir('test');
// 递归创建目录
mkdir('test/test1/test2', 0777, true);


/**
 * 为空，则为当前目录
 * 可以传相对路径，然后返回绝对路径
 * /Users/Univ/vagrant/php_demos/univ/elastica
 */
echo realpath('univ/elastica') . '<br>';
/**
 * ATHINFO_DIRNAME,
 * ATHINFO_BASENAME,
 * ATHINFO_EXTENSION,
 * ATHINFO_FILENAME,
 *
 */
var_dump(pathinfo(__FILE__));