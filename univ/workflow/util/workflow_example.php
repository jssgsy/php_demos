<?php
/**
 * User: univ <minglu.liu@beibei.com>
 * Date: 2020-05-14 10:56
 * @copyright Beidian Limited. All rights reserved.
 */
include "../thirdlib/workflows.php";

$w = new Workflows();

$visitorAlias = "otItd5am6otBiq-72oStq5vTo9lA";
// 第二个参数是输出的结果，要拷贝到clipboard中就需要设置
$w->result(3, $visitorAlias, 'visitorAlias', $visitorAlias, '');

$userId=4849;
$w->result(1, $userId, 'userId', $userId, '');

// 要输出少不了这句，可同时输出多行
echo $w->toxml();