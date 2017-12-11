<?php
/**
 * 微信请求的入口，验证自己服务器url的有效性
 * url:
 * User: Univ <minglu.liu@husor.com>
 * Date: 2017/12/11 19:18
 * @copyright Beidian Limited. All rights reserved.
 */



class WeixinIndexController extends CI_Controller {

    public static $TOKEN = 'univ';

    /**
     * 验证服务器url的有效性
     */
    public function validUrl() {
        //﻿获得参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];
        $token = self::$TOKEN;// 这里微信公众平台后台上自己填的token，两者需要保持一致
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];

        //第一步：形成数组，然后按字典序排序
        $tmpArr = array($nonce, $timestamp, $token);
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