<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/16
 * Time: 11:11
 */
//定位到上一级目录
require_once __DIR__ . '/..' . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

//定义消息回调函数
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

//消费名为hello队列中的消息
$channel->basic_consume('hello', '', false, true, false, false, $callback);

//callbacks是AMQPChannel类的一个数组成员变量，即消息队列中没有消息就一直等待
while(count($channel->callbacks)) {
    $channel->wait();
}

//消费方不能关闭channel与connection，否则就无法消费了。