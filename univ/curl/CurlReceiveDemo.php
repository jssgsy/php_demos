<?php
/**
 * 用来接收curl请求
 * Date: 2017/12/20 11:36
 * 注意：给请求响应时，数据的传递需要使用echo，就算传数据给微信服务器一样
 */

class CurlReceiveDemo {

    /**
     * 响应get请求
     */
    public function getMethodTest() {
        $responseMessage = [
            'name' => 'abc',
            'age' => 14
        ];
        // 返回数据给远程调用方，注意，是用echo
        echo json_encode($responseMessage);
    }

    /**
     * 响应get请求,带参数
     */
    public function getMethodWithParamTest() {
        // 获取请求参数，直接使用$_GET即可
        $name = $_GET['name'];
        $age = $_GET['age'];
        $name = empty($name) ? '名字出错了' : $name;
        $age = empty($age) ? '年龄出错了' : $age;
        echo 'get请求的参数为，name: ' . $name . ' ，age: ' . $age;
    }

    /**
     * 响应普通的post请求，可带多个参数
     */
    public function postMethodTest() {
        /*
         * 获取post请求参数，有两种方法：
         *  1. file_get_contents('php://input')，只适应于Content-Type为application/x-www-form-urlencoded，即以字符串方式传递参数
         *  2. $_POST['name']，$_POST['age']，适应于Content-Type为application/x-www-form-urlencoded和multipart/form-data
         */
        $postStr = file_get_contents('php://input');
        $postStr = empty($postStr) ? '参数出错了' : $postStr;
        echo 'post请求的参数为：' . $postStr;
    }

    /**
     * 响应图片上上传
     * 注意这里保存文件的方法:move_uploaded_file
     * 假设发送方表示文件的key为upload，此时$_FILES的内容如下：
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
    public function postMethodWithImageTest() {
        if (empty($_FILES)) {
            echo 'o o, 上传文件失败了，$_FILES为空';
        } else {
            // 将上传的文件保存到另一个位置,确保hello2.png文件有写入权限
            $save_result = move_uploaded_file($_FILES['upload']['tmp_name'], __DIR__ . DIRECTORY_SEPARATOR . 'hello2.png');
            if (empty($save_result)) {
                echo  'o o, 上传文件成功，但保存失败了';
            } else {
                echo '上传文件成功，且保存成功，$_FILES的内容为：' . json_encode($_FILES);
            }
        }
    }
}

$curlReceiveDemo =  new CurlReceiveDemo();

//$curlReceiveDemo->getMethodTest();

//$curlReceiveDemo->getMethodWithParamTest();

//$curlReceiveDemo->postMethodTest();

$curlReceiveDemo->postMethodWithImageTest();