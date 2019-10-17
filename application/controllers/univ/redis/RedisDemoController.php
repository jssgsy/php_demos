<?php
/**
 * Redis的简单使用
 * User: univ
 * Date: 2018/5/6 下午4:58
 * @copyright Beidian Limited. All rights reserved.
 */


class RedisDemoController extends CI_Controller {

    public function index() {
        // 1. 创建redis实例，并连接到redis服务器
        $redis = new Redis();
        // 这里的地址存疑
        $redis->connect('127.0.0.1', 6379);

        // 2. 存值（最简单的string类型）
        $redis->set('name', 'univ');

        // 3. 取值
        var_dump($redis->get('name'));

        // 上面就是使用redis的整个流程

        // ------------------------------------------------


        // 直接存对象或者数组类型
        // 只需要指定序列化方式即可。
        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

        $arr = [
            1 => 100,
            'name' => 'univ'
        ];
        $redis->set('arr', $arr);
        var_dump($redis->get('arr'));
    }

}