<?php
/**
 * 微信请求的入口，验证自己服务器url的有效性
 * url:http://45.78.19.224/php_demos/univ/weixin/index.php
 * Date: 2017/12/11 19:18
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

    /**
     * 删除用户标签
     */
    public function deleteUserTag() {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=' . $this->getAccessToken();
        $postField = [
            'tag' => [
                'id' => 101
            ]
        ];
        $postField = json_encode($postField);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        /*
         * {"errcode":0,"errmsg":"ok"}
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 删除用户标签失败';
        } else {
            return $data;
        }
    }

    /**
     * 获取用户列表(openid)
     */
    public function getUserList() {
        // next_openid,第一个拉取的OPENID，不填默认从头开始拉取
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $this->getAccessToken() . '&next_openid=';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*
         * next_openid：拉取列表的最后一个用户的OPENID，可利用此循环获取所有的用户
        {
            "total":3,
            "count":3,
            "data":{
                "openid":[
                    "oWcHH04vyv5XEV39j_J-5JbvyLxg",
                    "oWcHH0ztWUj0GydCCtl5Z8VU9iqY",
                    "oWcHH06gpuc5CBUNdrbgzMpd-UZA"
                ]
            },
            "next_openid":"oWcHH06gpuc5CBUNdrbgzMpd-UZA"
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取用户列表失败';
        } else {
            return $data;
        }
    }

    /**
     * 获取单个用户基本信息(UnionID机制),需要提供openid，openid可由getUserList方法获取
     * 这里只涉及openid,不涉及unionid
     */
    public function getUserBasicInfo() {
        // lang:返回国家地区语言版本,非必传
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getAccessToken() . '&openid=oWcHH0ztWUj0GydCCtl5Z8VU9iqY&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /*
         * subscribe:用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息;
         * sex:用户的性别，值为1时是男性，值为2时是女性，值为0时是未知;
         * unionid:只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段;
         * remark:公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注;
         * groupid:用户所在的分组ID;
         * tagid_list:用户被打上的标签ID列表;
        {
            "subscribe":1,
            "openid":"oWcHH0ztWUj0GydCCtl5Z8VU9iqY",
            "nickname":"UnivWX",
            "sex":1,
            "language":"en",
            "city":"杭州",
            "province":"浙江",
            "country":"中国",
            "headimgurl":"http://wx.qlogo.cn/mmopen/hQicLQFHWQTYRW9cCyQaficibQW9zRUoTXWc3fPjy2qhXU081ulFxFKkTUL5OFcEHciaHPmOvp51NbycG2o5KaUDTPdwJuWWauPQ/0",
            "subscribe_time":1513408617,
            "remark":"",
            "groupid":0,
            "tagid_list":[

            ]
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取用户基本信息失败';
        } else {
            return $data;
        }
    }

    /**
     * 批量用户基本信息(UnionID机制),需要提供openid，openid可由getUserList方法获取
     * post请求
     */
    public function batchGetUserBasicInfo() {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $this->getAccessToken();
        // lang非必传
        $postFields = [
            'user_list' => [
                ['openid' => 'oWcHH04vyv5XEV39j_J-5JbvyLxg','lang' => 'zh_CN'],
                ['openid' => 'oWcHH06gpuc5CBUNdrbgzMpd-UZA','lang' => 'zh_CN']
            ]
        ];
        $postFields = json_encode($postFields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        /*
         * 返回的结构与getUserBasicInfo返回的结构一致，只是这里是一个数组
         {
            "user_info_list":[
                {
                    "subscribe":1,
                    "openid":"oWcHH04vyv5XEV39j_J-5JbvyLxg",
                    "nickname":"许亮",
                    "sex":2,
                    "language":"zh_CN",
                    "city":"武汉",
                    "province":"湖北",
                    "country":"中国",
                    "headimgurl":"http://wx.qlogo.cn/mmopen/XJukapsrvDZVyXG2PBTc6wrQEB4Qp5OOhiaMv8QKOib1ZRUbcGAvicS3nz1ick4y3icfkm7ibTK4NBFJVp5hFqznbic9iaIFxyNYCIvk/0",
                    "subscribe_time":1512916771,
                    "remark":"",
                    "groupid":0,
                    "tagid_list":[

                    ]
                },
                {
                    "subscribe":1,
                    "openid":"oWcHH06gpuc5CBUNdrbgzMpd-UZA",
                    "nickname":"贝店",
                    "sex":0,
                    "language":"zh_CN",
                    "city":"",
                    "province":"",
                    "country":"",
                    "headimgurl":"http://wx.qlogo.cn/mmopen/XJukapsrvDZiaYwGYWnkddzYPpIo68Yguodia9MVh5vaF2QhoYgLe636icef04xXPBZmtG2tNGaP0TMgyDDK7DicpUCiaBKbAMse5/0",
                    "subscribe_time":1513067726,
                    "remark":"",
                    "groupid":0,
                    "tagid_list":[

                    ]
                }
            ]
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取用户基本信息失败';
        } else {
            return $data;
        }
    }

    /**
     * 获取用户地理位置
     * 开通了上报地理位置接口的公众号，用户在关注后进入公众号会话时，会弹框让用户确认是否允许公众号使用其地理位置。
     * 注意：弹框只会出现一次。但用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置（或者每隔5秒上报一次，取次于公众号的设置），上报地理位置以推送XML数据包到开发者填写的URL来实现。
     */
    public function getUserLocation() {
        /*
         * 微信服务器传送过来的xml数据格式
         */
        $xmlData = /** @lang text */
        <<<EOT
<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[LOCATION]]></Event>
<Latitude>23.137466</Latitude>
<Longitude>113.352425</Longitude>
<Precision>119.385040</Precision>
</xml>
EOT;
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $latitude = $postObj->Latitude;
        $longitude = $postObj->Longitude;
        $this->responseText('Latitude:' . $latitude . ' Longitude: ' . $longitude);
    }

    /**
     * 获取临时二维码ticket
     *
     */
    public function getTempQrcodeTicket() {
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        /*
         * 发送给微信服务器的数据
         * action_name：
         *  QR_SCENE：临时的整型参数值;
         *  QR_STR_SCENE: 临时的字符串参数值;
         *  QR_LIMIT_SCENE: 永久的整型参数值;
         *  QR_LIMIT_STR_SCENE: 永久的字符串参数值;
         * scene_id: 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
         */
        $postFields = [
            'expire_seconds' => 300,
            'action_name' => 'QR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => 123456
                ]
            ]
        ];
        $postFields = json_encode($postFields);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        /*
         * url:二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
        {
            "ticket":"gQFE8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyOEV4TFFXcDFmN2oxOWZPWHhxMS0AAgQjsTtaAwQsAQAA",
            "expire_seconds":300,
            "url":"http://weixin.qq.com/q/028ExLQWp1f7j19fOXxq1-"
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取临时二维码ticket失败';
        } else {
            return $data;
        }
        curl_close($ch);
    }

    /**
     * 获取永久二维码ticket
     */
    public function getPermanentQrcodeTicket() {
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        /*
         * 发送给微信服务器的数据
         * action_name：四个候选值如下：
         *  QR_SCENE：临时的整型参数值;
         *  QR_STR_SCENE: 临时的字符串参数值;
         *  QR_LIMIT_SCENE: 永久的整型参数值;
         *  QR_LIMIT_STR_SCENE: 永久的字符串参数值;
         * scene_str: 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
         */
        $postFields = [
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_str' =>'abcdefg'
                ]
            ]
        ];
        $postFields = json_encode($postFields);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        /*
         {
            "ticket":"gQEj8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3pPdlJCcDFmN2oxMDAwME0wM1MAAgT6sTtaAwQAAAAA",
            "url":"http://weixin.qq.com/q/02WzOvRBp1f7j10000M03S"
         }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取永久二维码ticket失败';
        } else {
            return $data;
        }
        curl_close($ch);
    }

    /**
     * 生成带参数的二维码
     * 分成两步：
     *  1. 获取ticket,ticket可由上面的getTempQrcodeTicket或者getPermanentQrcodeTicket取得；
     *  2. 获取带参数的二维码；
     * 有了ticket之后，可以直接在浏览器中输入https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET以获取二维码；
     * 扫码时，如果用户还未关注公众号，会提示用户关注公众号，如果用户已经关注公众号，在用户扫描后会自动进入会话
     * 下面演示将微信服务器返回的二维码进行保存
     */
    public function getSceneQrcode() {
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQEj8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3pPdlJCcDFmN2oxMDAwME0wM1MAAgT6sTtaAwQAAAAA';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // ticket正确情况下，http 返回码是200，是一张图片，可以直接展示或者下载
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 生成带参数的二维码失败';
        } else {
            // 注意要确保服务器上同目录下的qrcode.png有写入权限
            file_put_contents('qrcode.png', $data);
            return 'o o, 生成带参数的二维码成功，保存在了qrcode.png中';
        }
        curl_close($ch);
    }

    /**
     * 响应扫描带参数二维码事件
     * 用户扫描带场景值二维码时，可能推送以下两种事件：
        1. 如果用户还未关注公众号，则用户可以关注公众号，关注后微信会将带场景值关注事件推送给开发者。
        2. 如果用户已经关注公众号，则微信会将带场景值扫描事件推送给开发者。
     */
    public function replyScanSceneQrcode() {
        // 未关注时扫码微信服务器发送过来的xml数据格式
        /*
         * EventKey:事件KEY值，qrscene_为前缀，后面为二维码的参数值
         */
        $unscribeXmlData = /** @lang text */
            <<< EOT
<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[qrscene_123123]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>
EOT;
        // 关注时扫码微信服务器发送过来的xml数据格式
        /*
         * EventKey:事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
         */
        $unscribeXmlData = /** @lang text */
            <<< TAG
<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime><MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[SCENE_VALUE]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>
TAG;
        $postStr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $event = $postObj->Event;
        $eventKey = $postObj->EventKey;
        $ticket = $postObj->Ticket;
        if ('subscribe' == $event) {
            // 在用户扫描二维码并点击关注后，这里的消息会回复给用户
            $this->responseText('这是扫码二维码关注的。');
        } elseif ('SCAN' == $event) {
            // 在已关注用户扫描二维码进入公众号时，这里的消息会回复给用户
            $this->responseText('hello, 老朋友，你已经关注了。');
        } else {
            $this->responseText('o o, 我还不能识别。');
        }
        // file_put_contents('log', 'EventKey: ' . $eventKey . ' Ticket: ' . $ticket);
    }

    /**
     * 长链接转短链接
     * 主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，
     *  将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。
     */
    public function longUrl2shortUrl() {
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        /*
         * action: 填long2short，代表长链接转短链接
         */
        $postFields = [
            'action' => 'long2short',
            'long_url' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQEj8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyV3pPdlJCcDFmN2oxMDAwME0wM1MAAgT6sTtaAwQAAAAA'
        ];
        $postFields = json_encode($postFields);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        /*
        {
            "errcode":0,
            "errmsg":"ok",
            "short_url":"https://w.url.cn/s/Apyc75i"
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 长链接转短链接失败';
        } else {
            return $data;
        }
        curl_close($ch);
    }

    /**
     * 模板消息-设置行业信息
     * 设置行业可在微信公众平台后台完成，每月可修改行业1次，帐号仅可使用所属行业中相关的模板，为方便第三方开发者，提供通过接口调用的方式来修改账号所属行业
     */
    public function setIndustry() {
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        /*
         * industry_id1、industry_id2：公众号模板消息所属行业编号
         * 1：IT科技-》互联网/电子商务；
         * 4：IT科技-》电子技术；
         * 其它的需要参考微信官网；
         */
        $postFields = [
            'industry_id1' => '1',
            'industry_id2' => '4'
        ];
        $postFields = json_encode($postFields);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        // {"errcode":0,"errmsg":"ok"}
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 设置行业信息失败';
        } else {
            return $data;
        }
        curl_close($ch);
    }

    /**
     * 模板消息-获取行业信息
     * 这里以获取上面设置的行业信息为例
     */
    public function getIndustry() {
        $url = 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=' . $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*
         {
            "primary_industry":{
                "first_class":"IT科技",
                "second_class":"互联网|电子商务"
            },
            "secondary_industry":{
                "first_class":"IT科技",
                "second_class":"电子技术"
            }
        }
         */
        $data = curl_exec($ch);
        if (empty($data)) {
            return 'o o, 获取行业信息失败';
        } else {
            return $data;
        }
        curl_close($ch);
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

// 下面两句是一体的
/*$data = $weixinIndex->getUserTag();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->deleteUserTag();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getUserList();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getUserBasicInfo();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->batchGetUserBasicInfo();
var_dump($data);*/

//$weixinIndex->getUserLocation();

// 下面两句是一体的
/*$data = $weixinIndex->getTempQrcodeTicket();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getPermanentQrcodeTicket();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->getSceneQrcode();
var_dump($data);*/

//$weixinIndex->replyScanSceneQrcode();

// 下面两句是一体的
/*$data = $weixinIndex->longUrl2shortUrl();
var_dump($data);*/

// 下面两句是一体的
/*$data = $weixinIndex->setIndustry();
var_dump($data);*/

$data = $weixinIndex->getIndustry();
var_dump($data);