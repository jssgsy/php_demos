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

    // 以下为微信提供的7种消息类型
    public static $MESSAGE_TYPE_TEXT = 'text';  // 文本消息
    public static $MESSAGE_TYPE_IMAGE = 'image'; // 图片消息
    public static $MESSAGE_TYPE_VOICE = 'voice'; // 语音消息
    public static $MESSAGE_TYPE_VIDEO = 'video'; // 视频消息
    public static $MESSAGE_TYPE_SHORTVIDEO = 'shortvideo'; // 小视频消息
    public static $MESSAGE_TYPE_LOCATION = 'location'; // 地理位置消息
    public static $MESSAGE_TYPE_LINK = 'link'; // 链接消息

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

    // 微信服务器要求的图片消息的响应格式
    public static $MESSAGE_TYPE_IMAGE_TEMPLATE = /** @lang text */
        <<<TAG
        <xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <Image><MediaId><![CDATA[%s]]></MediaId></Image>
        </xml>
TAG;

    /**
     * 验证服务器地址的有效性，只需首次验证一次
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
     * 回复图片给用户
     * 注意，这里需要MediaId,这里采用的是临时素材，所以三天后会失效
     */
    public function sendImageToUser() {
        $postStr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        echo sprintf(self::$MESSAGE_TYPE_IMAGE_TEMPLATE, $fromUsername, $toUsername, time(), 'image', 'C3jRK5ptZ9MGPTInTdUq1Mn19JFPA7fGsAwsQerylt4n6a0KA2IAzViVZorWeevM');
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

    /**
     * 接收菜单点击事件，这里以click事件为例(点击view事件时会自动跳转到相应的url处)
     * 注意，不同的事件，微信服务器发送过来的 xml 数据格式不完全一致，需要参考微信官网，但可以根据event进行判断
     * 菜单项在createMenu方法中定义
     */
    public function responseMenuEvent() {
        $postStr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $event = $postObj->Event;
        $eventKey = $postObj->EventKey;
        if ($event == 'CLICK') {
            if ($eventKey == 'V1001_TODAY_MUSIC') {
                return '你点击了今日歌曲';
            } elseif ($eventKey == 'V1001_GOOD') {
                return '欢迎赞一下';
            } else {
                return 'it is impossible';
            }
        } else {
            return '点击的不是click菜单';
        }
    }

    /**
     * 获取微信服务器IP地址
     * 返回一个json字符串
     * {
        "ip_list":[
            "101.226.62.77",
            "101.226.62.78",
            "101.226.62.79",
            "101.226.62.80"
         ]
       }
     */
    public function getWeixinServerIp() {
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 获取自定义菜单配置接口,与getMenu()方法类似，但返回的数据更多
     */
    public function getMenuConf() {
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return '未获取到自定义菜单配置';
        }
        return $data;
    }

    /**
     * 根据不同类型回复相应的消息
     * 微信提供的消息总共有7种类型
     * 注意，此方法内部调用了responseText方法，而responseText方法内部又使用了php://input去获取微信服务器传过来的信息，经验证这是可行的
     */
    public function responseMessage() {
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $messageType = $postObj->MsgType;
        switch ($messageType) {
            case self::$MESSAGE_TYPE_TEXT:
                $this->responseText('你发送的是文本消息');
                break;
            case self::$MESSAGE_TYPE_IMAGE:
                $this->responseText('你发送的是图片消息');
                break;
            case self::$MESSAGE_TYPE_VOICE:
                $this->responseText('你发送的是语音消息');
                break;
            case self::$MESSAGE_TYPE_VIDEO:
                $this->responseText('你发送的是视频消息');
                break;
            case self::$MESSAGE_TYPE_SHORTVIDEO:
                $this->responseText('你发送的是小视频消息');
                break;
            case self::$MESSAGE_TYPE_LOCATION:
                $this->responseText('你发送的是地理位置消息');
                break;
            case self::$MESSAGE_TYPE_LINK:
                $this->responseText('你发送的是链接消息');
                break;
            default:
                $this->responseText('厉害了，这个消息类型我不能识别');
        }
    }

    /**
     * 获取公众号的自动回复规则
     * 如关注后自动回复是否开启、消息自动回复是否开启、关注后自动回复的信息等等
     */
    public function getAutoReplyRule() {
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 返回的是json格式的字符串
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取失败';
        } else {
            return $data;
        }
        curl_close($ch);
    }

    /**
     * 上传(新增)临时素材
     * 注意：
     *  1. (至少在php7中)curl中已经不支持使用@后接媒体文件的写法，可以使用CURLFile代替；
     *  2. CURLOPT_SAFE_UPLOAD选项也已经废弃；
     */
    public function uploadTempMaterial() {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='. $this->getAccessToken() . '&type=image';
        // php7中(好像是php5.6以后)curl已经不支持@加媒体文件的写法
        // $postField = array('media' => '@' . realpath("hello.png"));  ;

        // 注意下面CURLFile类的使用,media是微信接口的入参要求，需要同目录下有hello.png文件存在
        $postField = array('media' => new CURLFile(realpath("hello.png")));  ;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        // 返回的是json格式的字符串
        // {"type":"image","media_id":"C3jRK5ptZ9MGPTInTdUq1Mn19JFPA7fGsAwsQerylt4n6a0KA2IAzViVZorWeevM","created_at":1513732526}
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return 'o o, 上传失败了';
        } else {
            return $data;
        }
    }

    /**
     * 获取临时素材
     * 注意，视频文件不支持https下载，调用该接口需http协议,当然其它类型是可以使用http协议的，如这里的图片
     */
    public function getTempMaterial() {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->getAccessToken() . '&media_id=C3jRK5ptZ9MGPTInTdUq1Mn19JFPA7fGsAwsQerylt4n6a0KA2IAzViVZorWeevM';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return 'o o, 获取临时素材失败了';
        } else {
            return $data;
        }
    }

    /**
     * 上传(新增)永久素材，这里以图片为例。与上面的上传临时素材唯一的差别是url的不同
     * 永久素材分为：
     *  图文；
     *  其它(如图片，视频等);
     */
    public function uploadPermanentMaterial() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='. $this->getAccessToken() . '&type=image';
        // php7中(好像是php5.6以后)curl已经不支持@加媒体文件的写法
        // $postField = array('media' => '@' . realpath("hello.png"));  ;

        // 注意下面CURLFile类的使用,media是微信接口的入参要求，需要同目录下有hello.png文件存在
        $postField = array('media' => new CURLFile(realpath("hello.png")));  ;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        // 返回的是json格式的字符串
        // {"media_id":"eNVpPb0KQciogNvj0nyA8HXNWo1K55yJeoNX00OCc6M","url":"http:\/\/mmbiz.qpic.cn\/mmbiz_png\/lwz6EUkeG8x3jfZlEibFCzfymOEMZmH1l9wfdEK5Ot2aTQkDqibKeiafl0iaMtbpdKXahN0ll03sEUWicn5PFNcrIdg\/0?wx_fmt=png"}
        // 其中的url为新增的图片素材的图片URL（仅新增图片素材时会返回该字段）
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return 'o o, 上传失败了';
        } else {
            return $data;
        }
    }

    /**
     * 获取素材列表
     * 注意
     *  1. 请求方式为post；
     *  2. 包含公众号在公众平台官网素材管理模块中新建的图文消息、语音、视频等素材；
     *  3. 临时素材无法通过本接口获取；
     */
    public function getMaterialList() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . $this->getAccessToken();
        // 三个字段是微信接口的要求
        /**
         * type:素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）;
         * offset:从全部素材的该偏移位置开始返回，0表示从第一个素材 返回;
         * count:返回素材的数量，取值在1到20之间
         */
        $postField = [
            'type' => 'image',
            'offset' => 0,
            'count' => 2
        ];
        /*
         * 特别注意，微信许多接口如果是post请求，则参数一般要求是json格式的字符串！
         */
        $postField = json_encode($postField);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        /*
        {
            "item":[
                {
                    "media_id":"eNVpPb0KQciogNvj0nyA8BYUnUnXcs52L7j6_0ICjYA",
                    "name":"/usr/local/nginx-1.13.6/html/php_demos/univ/weixin/hello.png",
                    "update_time":1513737843,
                    "url":"http://mmbiz.qpic.cn/mmbiz_png/lwz6EUkeG8x3jfZlEibFCzfymOEMZmH1l9wfdEK5Ot2aTQkDqibKeiafl0iaMtbpdKXahN0ll03sEUWicn5PFNcrIdg/0?wx_fmt=png"
                },
                {
                    "media_id":"eNVpPb0KQciogNvj0nyA8MpmAv3RCh9slS2y20udljE",
                    "name":"/usr/local/nginx-1.13.6/html/php_demos/univ/weixin/hello.png",
                    "update_time":1513737837,
                    "url":"http://mmbiz.qpic.cn/mmbiz_png/lwz6EUkeG8x3jfZlEibFCzfymOEMZmH1l9wfdEK5Ot2aTQkDqibKeiafl0iaMtbpdKXahN0ll03sEUWicn5PFNcrIdg/0?wx_fmt=png"
                }
            ],
            "total_count":3,
            "item_count":2
        }
         */
        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return 'o o, 获取素材列表失败了';
        } else {
            return $data;
        }
    }

    /**
     * 获取永久素材
     * 注意:
     *  1. 这里需要为post请求;
     *  2. 可通过上面的"获取素材列表(getMaterialList)"获知素材的media_id;
     *  3. 图文素材与视频消息素材返回的是一个json字符串，其它类型的素材返回的直接为素材的内容，开发者可以自行保存为文件
     */
    public function getPermanentMaterial() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . $this->getAccessToken();
        // 特别注意，微信许多接口如果是post请求，则参数一般要求是json格式的字符串！
        $postField = [
            'media_id' => 'eNVpPb0KQciogNvj0nyA8BYUnUnXcs52L7j6_0ICjYA'
        ];
        $postField = json_encode($postField);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);

        $data = curl_exec($ch);
        curl_close($ch);
        if (empty($data)) {
            return 'o o, 获取永久素材失败了';
        } else {
            return $data;
        }
    }

    /**
     * 获取素材总数,只包含永久素材，临时素材不计算在内
     * 永久素材的总数，也会计算公众平台官网素材管理中的素材
     */
    public function getMaterialCount() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*
         {
            "voice_count":0,
            "video_count":0,
            "image_count":3,
            "news_count":0
         }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取素材总数失败了';
        } else {
            return $data;
        }
    }

    /**
     * 删除永久素材
     * 注意：
     *  1、请谨慎操作本接口，因为它可以删除公众号在公众平台官网素材管理模块中新建的图文消息、语音、视频等素材（但需要先通过获取素材列表来获知素材的media_id）；
     *  2、临时素材无法通过本接口删除；
     */
    public function deletePermanentMaterial() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=' . $this->getAccessToken();
        $postField = [
            'media_id' => 'eNVpPb0KQciogNvj0nyA8BYUnUnXcs52L7j6_0ICjYA'
        ];
        $postField = json_encode($postField);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        /*
         {"errcode":0,"errmsg":"ok"}
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 删除永久素材失败了';
        } else {
            return $data;
        }
    }

    /**
     * 创建(用户)标签
     */
    public function createUserTag() {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=' . $this->getAccessToken();
        $postField = [
            'tag' => [
                'name' => 'family'
            ]
        ];
        $postField = json_encode($postField);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        /*
         * {"tag":{"id":100,"name":"family"}}
         * id:标签id，由微信分配;
         * name:标签名，UTF8编码
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 创建用户标签失败';
        } else {
            return $data;
        }
    }

    /**
     * 获取公众号已创建的标签
     */
    public function getUserTag() {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*
         * count:此标签下的粉丝数
         {
            "tags":[
                {
                    "id":2,
                    "name":"星标组",
                    "count":0
                },
                {
                    "id":100,
                    "name":"family",
                    "count":0
                },
                {
                    "id":101,
                    "name":"hangzhou",
                    "count":0
                }
            ]
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取用户标签失败';
        } else {
            return $data;
        }
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
/*$deleteResult = $weixinIndex->deleteMenu();
var_dump($deleteResult);*/

// 下面两句是一体的
/*$result = $weixinIndex->responseMenuEvent();
$weixinIndex->responseText($result);*/

// 下面两句是一体的
/*$ipList = $weixinIndex->getWeixinServerIp();
var_dump($ipList);*/

// 下面两句是一体的
/*$menuConf = $weixinIndex->getMenuConf();
var_dump($menuConf);*/

//$weixinIndex->responseMessage();

// 下面两句是一体的
/*$data = $weixinIndex->getAutoReplyRule();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->uploadTempMaterial();
var_dump($data);*/

//$weixinIndex->sendImageToUser();

// 下面两句是一体的
/*$data = $weixinIndex->getTempMaterial();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->uploadPermanentMaterial();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getMaterialList();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getPermanentMaterial();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getMaterialCount();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->deletePermanentMaterial();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->createUserTag();
var_dump($data);*/

$data = $weixinIndex->getUserTag();
var_dump($data);

