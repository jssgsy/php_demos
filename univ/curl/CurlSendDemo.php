<?php
/**
 * 模拟curl发送请求
 * Created by PhpStorm.
 * User: minglu.liu
 * Date: 2017/6/1
 * Time: 10:43
 */

/**
 * 关于cURL的简单用法
 * 1. 重点在于其流程:
    1. 初始化cURL会话
    $ch = curl_init();

    //2. 设置cURL的各种选项（属性）如请求的url等
    curl_setopt($ch, CURLOPT_URL, '');

    //3. 执行cURL会话
    $data = curl_exec($ch);

    //4. 关闭cURL会话
    curl_close($ch);

    var_dump($data);
 *
 * 2. curl_setopt的第二个参数有很多只能设置为关闭(false)或者开启(true，常用1表示)，也有一些只能设置为integer或者string
 * curl_setopt的几个重要的参数：
 *  CURLOPT_URL：请求的url；
 *  CURLOPT_RETURNTRANSFER：TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出;
 *  CURLOPT_POST：TRUE时会发送POST请求，默认类型为application/x-www-form-urlencoded(以字符串形式发送请求参数)；
 *  CURLOPT_SAFE_UPLOAD：PHP7中删除了此选项， 必须使用CURLFile来上传文件
 *  CURLOPT_POSTFIELDS：值可以是类似'para1=val1&para2=val2&...'的字符串，也可以使用一个以字段名为键值，字段数据为值的数组
 *  CURLOPT_HEADER：启用时会将头文件的信息作为数据流输出。
 *
 */
class CurlSendDemo {

    // 便于测试，这里路径统一为
    public static $URL = 'http://45.78.19.224/php_demos/univ/curl/CurlReceiveDemo.php';

    /**
     * 发送最基本的get请求
     */
    public function sendGetMethod() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 发送最基本的get请求,带参数
     * get请求，参数是附在url中的，所有和发送普通的get请求没有任何差别
     */
    public function sendGetMethodWithParam() {
        $paramStr = 'name=abc&age=34';
        $url = self::$URL . '?' . $paramStr;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 发送最基本的post请求,可带多个参数
     * 重要！
     */
    public function sendPostMethod() {
        // 通过post方式发送，和普通表单一样，注意，有的服务器要求参数形式以json格式的字符串，当然这说明服务器会对之进行解析
        // 字符串的方式
        /*
         * 发送post请求参数有两种方式：
         * 1. 以字符串的形式，此时Content-Type会被设置成默认的application/x-www-form-urlencoded;
         *      此时服务器端有两种方式可以获取到：
         *          1. 用file_get_contents('php://input')可以获取到整个请求字符串；
         *          2. 用$_POST可以获取到单个请求参数；
         * 2. 以数组的形式，此时Content-Type会被设置成的multipart/form-data；
         *      此时服务器端只能用$_POST方式获取到请求参数
         * 小结：由此可知，服务器端用$_POST来获取post请求参数最保险
         */
        $postFields='name=abc&age=34';
        /*$postFields = [
            'name' => 'abc',
            'age' => 45
        ];*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 上传文件，这里以图片为例
     * php5.6(至少php7)以后，php中的curl已经不支持@后接媒体文件的方式，需要使用CurlFile上传图片
     */
    public function sendPostMethodWithImage() {
        $ch = curl_init();
        $filePath = realpath('hello.png');
        // upload名称是自定义的,php5.6以后不能使用@hello.png的方式了
        $postField = [
            'upload' => new CURLFile($filePath)
        ];
        curl_setopt($ch, CURLOPT_URL, self::$URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
        /*
         * 下面是返回的$_FILES
         * application/octet-stream：只能提交二进制，而且只能提交一个二进制，如果提交文件的话，只能提交一个文件,后台接收参数只能有一个
         * tmp_name：服务器端可使用$_FILES['upload']['tmp_name']引用上传的文件，此时可以利用move_uploaded_file方法将文件保存到另一个地方
         {
            "upload":{
                "name":"hello.png",
                "type":"application/octet-stream",
                "tmp_name":"/tmp/phpY5m0rc",
                "error":0,
                "size":564088
            }
         }
         */
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}

$curlSendDemo = new CurlSendDemo();

// 下面两句是一体的
/*$result = $curlSendDemo->sendGetMethod();
var_dump($result);*/

// 下面两句是一体的
/*$result = $curlSendDemo->sendGetMethodWithParam();
var_dump($result);*/

// 下面两句是一体的
/*$result = $curlSendDemo->sendPostMethod();
var_dump($result);*/

$data = $curlSendDemo->sendPostMethodWithImage();
var_dump($data);
