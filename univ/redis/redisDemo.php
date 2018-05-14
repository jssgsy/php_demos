<?php
/**
 * 描述...
 * User: univ <minglu.liu@beibei.com>
 * Date: 2018/5/6 下午4:54
 * @copyright Beidian Limited. All rights reserved.
 */


$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
var_dump($redis);
