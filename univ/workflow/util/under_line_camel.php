<?php
/**
 * 下划线与驼峰还互转
 * User: univ <minglu.liu@beibei.com>
 * Date: 2020-01-10 20:18
 * @copyright Beidian Limited. All rights reserved.
 */

require_once('../thirdlib/workflows.php');
$w = new Workflows();

$input = $argv[1];
$pattern = '/[A-Z]/';
$result = "";

if (preg_match($pattern, $input)) {
    // 说明是陀峰转下划线
    $result = preg_replace_callback($pattern, function ($matched) {
        // 匹配到的大写之母
        $upperChar = $matched[0];
        return "_" . strtolower($upperChar);

    }, $input);
    $result =  substr($result, 1);
    $w->result( 1, $result, '转换后的下划线为：', $result, '');
    echo $w->toxml();
} else {
    // 下划线转陀峰
    $arr = explode("_", $input);
    foreach ($arr as $item) {
        $result .= ucwords($item);
    }
    //echo $result;
    $w->result( 2, $result, '转换后的驼峰为：', $result, '');
    echo $w->toxml();
}