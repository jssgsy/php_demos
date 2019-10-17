<?php
/**
 * 时间转换
 *  时间戳 ---> 字符串格式的时间
 *  字符串格式的时间 ---> 时间戳
 *
 * User: univ <minglu.liu@beibei.com>
 * Date: 2019/8/14 11:52 AM
 * @copyright Beidian Limited. All rights reserved.
 */
require_once('../thirdlib/workflows.php');
$w = new Workflows();

date_default_timezone_set('Asia/Shanghai');
$input = $argv[1];
if (!empty($input)) {
    $w->result(1, $input, '输入的内容为:', $input, '');

    // method -> 小写的以逗号分隔的method
    $pattern = '/[A-Z]/';
    if (preg_match($pattern, $input)) {
        $result = preg_replace_callback($pattern, function ($mathched) {
            // 通常: $matches[0]是完成的匹配
            return '.' . strtolower($mathched[0]);
        }, $input);

        $result = substr($result, 1);
        $w->result( 2, $result, '转换后的method：', $result, '');
        // 这句话竟然不能少，比较奇怪
        echo $w->toxml();
    }

    // 时间戳转时间
    $date = date('Y-m-d H:i:s', $input);
    if (!empty($date)) {
        $w->result(3, $date, '转换后的时间为:', $date, '');
    }

    // 时间转时间戳
    $time = strtotime($input);
    if (!empty($time)) {
        $w->result(4, $time, '转换后的时间戳:', $time, '');
    }

    // 多行转一行，以逗号分开
    $pattern1 = '/\n/';
    if (preg_match($pattern1, $input)) {
        $str = preg_replace($pattern1, ',', $input);
        $w->result(5, $str, '多行转一行：', $str, '');
        // 这句话竟然不能少，比较奇怪
        echo $w->toxml();
    }
    echo $w->toxml();


} else {
    // 显示两个数据项，当前时间与对应的时间戳
    $date = date('Y-m-d H:i:s');
    $time = time();
    $w->result(1, $time, '当前时间戳:', $time, '');
    $w->result(2, $date, '当前时间:', $date, '');
    echo $w->toxml();
}

?>