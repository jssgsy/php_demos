<?php
/**
 * Created by PhpStorm.
 * User: minglu.liu
 * Date: 2017/6/1
 * Time: 10:43
 */

/**
 * 关于cURL的简单用法
 * 1. 重点在于其流程：整个cURL的流程就是下面的1，2，3，4点;
 * 2. curl_setopt的第二个参数有很多只能设置为关闭(false)或者开启(true，常用1表示)，也有一些只能设置为integer或者string
 */
//1. 初始化cURL会话
$ch = curl_init();

//2. 设置cURL的各种选项（属性）如请求的url等
curl_setopt($ch, CURLOPT_URL, 'http://www.baidu.com');
//设置为TRUE会将curl_exec()获取的信息以字符串返回，而不是直接输出。
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//设置为TRUE会将头文件的信息作为数据流输出。
curl_setopt($ch,CURLOPT_HEADER, 1);

//3. 执行cURL会话
$data = curl_exec($ch);

//4. 关闭cURL会话
curl_close($ch);

var_dump($data);