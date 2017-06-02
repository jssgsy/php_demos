<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/6/3
 * Time: 00:05
 */

/**
 * 这里演示最基本的memcache的用法
 * memcache在php中的用法很简单，核心在于环境的正确配置(参见为知笔记)：
 * 1. memcached服务器要先安装好并启动；
 * 2. php的memcache扩展要安装好；
 */

//1. 创建
$memcache = new Memcache();

//2. 打开一个memcached服务端连接,默认端口为11211
$memcache->connect('localhost');

//3. 添加数据到memcached服务器中
$memcache->add('univ_name', 'fcsnwu');
$memcache->add('univ_age', 27);

//4. 从memcached服务器中获取数据
var_dump($memcache->get(['univ_name', 'univ_age']));

//5. 关闭连接
$memcache->close();
