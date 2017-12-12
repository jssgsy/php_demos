<?php
/**
 * 微信请求的入口，验证自己服务器url的有效性
 * url:http://45.78.19.224/php_demos/univ/weixin/index.php
 * User: Univ <minglu.liu@husor.com>
 * Date: 2017/12/11 19:18
 * @copyright Beidian Limited. All rights reserved.
 */
class WeixinIndex {

    public static $TOKEN = 'univ';  // 微信公众平台后台上自己填的token，两者需要保持一致

    /**
     * 验证服务器地址的有效性
     */
    public function validUrl() {
        //﻿获得微信服务器传过来的参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];    // 微信服务器传过来的随机数
        $timestamp = $_GET['timestamp'];    // 微信服务器传过来的时间戳
        $echostr = $_GET['echostr'];    // 微信服务器传过来的随机字符串
        $signature = $_GET['signature'];    // 微信服务器传过来的签名

        //第一步：形成数组，然后按字典序排序
        $tmpArr = array($nonce, $timestamp, self::$TOKEN);
        sort($tmpArr, SORT_STRING);

        //第二步：拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($tmpArr));

        //第三步：获得加密后的字符串与signature对比
        if ($str == $signature && $echostr) {
            //返回给微信服务器
            echo $echostr;
            exit;
        }
    }
}

$weixinIndex = new WeixinIndex();
$weixinIndex->validUrl($nonce, $timestamp, $echostr, $signature);