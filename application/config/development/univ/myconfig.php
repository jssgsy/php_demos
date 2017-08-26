<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/31
 * Time: 22:10
 */

/**
 * 假设当前环境为development，则load('univ/myconfig')时会尝试顺序加载两个文件：
 *  1. config/univ/myconfig.php;
 *  2. config/ENVIRONMENT/univ/myconfig.php;
 * 补充：
 *  1. config目录下的配置文件为全局配置文件，不认哪种环境都会加载；
 *  2. config/ENVIRONMENT目录下的配置文件为特定环境的配置文件；
 *  3. 先加载全局配置文件，再加载特定环境的配置文件
 */
$config['univ_name_dev'] = 'fcsnwu__dev';