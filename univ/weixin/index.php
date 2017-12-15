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
    // 测试号
    public static $APP_ID = 'wx48038a68dbdb007e';
    public static $APP_SECRET = '9a37010142cb479e99360b2fef0c239f';
    public static $ACCESS_TOKEN_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

    public static $MESSAGE_TYPE_TEXT = 'text';  // 文本消息

    // 微信服务器要求的文本消息的响应格式
    public static $MESSAGE_TYPE_TEXT_TEMPLATE = /** @lang text */
        <<<TAG
        <xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>
TAG;

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

    /**
     * 响应文本消息，用户发什么，就回复什么
     * 注意，﻿a给b发送消息，a是FromUserName,b是ToUserName，反过来，b给a回复消息，b是FromUserName，a是ToUserName
     * @param string $text  要回复的文本内容，默认回复用户发的内容
     */
    public function responseText($text = '') {
        // 不要使用下面的语句，因为默认是禁用register_globals
        // $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postStr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $content = $postObj->Content;

        // 下面这句是为了便于此方法被其它方法调用
        $content = empty($text) ? $content : $text;
        echo sprintf(self::$MESSAGE_TYPE_TEXT_TEMPLATE, $fromUsername, $toUsername, time(), self::$MESSAGE_TYPE_TEXT, $content);
    }

    /**
     * 订阅公众号事件
     * 核心思维：用户订阅公众号时，回复用户的也是一种消息类型，如下面的文本类型，所以，并不是和文本消息一样有响应格式的要求
     */
    public function subscribe() {
        $postStr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $event = $postObj->Event;
        $str = 'unknow message';
        if ('subscribe' == $event) {
            $str = '欢迎订阅此公众号，孔繁歧';
        }
        echo sprintf(self::$MESSAGE_TYPE_TEXT_TEMPLATE, $fromUsername, $toUsername, time(), self::$MESSAGE_TYPE_TEXT, $str);
    }

    /**
     * 获取 accessToken，注意，accessToken 有效期为7200秒
     * 利用 curl，所以需要先使 curl 支持 https 协议
     */
    public function getAccessToken() {
        $url = self::$ACCESS_TOKEN_URL_PREFIX . '&appid=' . self::$APP_ID . '&secret=' . self::$APP_SECRET;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // 不直接输出，而是以字符串的形式返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        // 为便于测试，直接将 accessToken 返回
        $this->responseText($data);
    }
}

/**
 * 运行时注释其它
 */
$weixinIndex = new WeixinIndex();
//$weixinIndex->validUrl($nonce, $timestamp, $echostr, $signature);
//$weixinIndex->responseText();
//$weixinIndex->subscribe();
$weixinIndex->getAccessToken();