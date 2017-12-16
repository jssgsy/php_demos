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
            $str = '欢迎订阅此公众号，学姐好';
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
        curl_close($ch);
        if (!empty($data)) {
            $data = json_decode($data);
            return $data->access_token;
        } else {
            return '获取access_token失败';
        }
    }

    /**
     * 自定义菜单，注意，菜单还有所谓的人性化菜单
     * 随便学习如何在php中使用curl发送post请求
     * 注意，现在有小程序的类型，在绑定小程序的 appid 之前不要在这里出现
     */
    public function createMenu() {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // 控制返回的数据中是否包含头信息
        curl_setopt($ch, CURLOPT_HEADER, 1);
        // 设置为post请求
        curl_setopt($ch, CURLOPT_POST, 1);
        $post_fields = <<<EOT
{
     "button":[
     {    
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {    
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }
EOT;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);
    }

    /**
     * 查询自定义的菜单
     * 在设置了个性化菜单后，使用本自定义菜单查询接口可以获取默认菜单和全部个性化菜单信息。
     */
    public function getMenu() {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $menus = curl_exec($ch);
        curl_close($ch);
        return $menus;
    }

    /**
     * 删除自定义的菜单
     */
    public function deleteMenu() {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(CURLOPT_HEADER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}

/**
 * 运行时注释其它
 */
$weixinIndex = new WeixinIndex();
//$weixinIndex->validUrl($nonce, $timestamp, $echostr, $signature);
//$weixinIndex->responseText();
//$weixinIndex->subscribe();

// 下面两句是一体的
/*$access_token = $weixinIndex->getAccessToken();
$weixinIndex->responseText($access_token);*/

//$weixinIndex->createMenu();

// 下面两句是一体的
/*$menus = $weixinIndex->getMenu();
var_dump($menus);*/

// 下面两句是一体的
$deleteResult = $weixinIndex->deleteMenu();
var_dump($deleteResult);