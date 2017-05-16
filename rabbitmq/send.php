<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/5/16
 * Time: 11:11
 * 消息发送者
 *
 */
//定位到上一级目录
require_once __DIR__ . '/..' . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

//获取一个channel
$channel = $connection->channel();

//声明一个队列，hello是队列的名字
$queue = $channel->queue_declare('hello', false, false, false, false);

//声明一个消息，注意，不是普通的字符串
$message = new AMQPMessage('hello, rabbitmq');

//将消息发送到rabbitmq上
$channel->basic_publish($message, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

//关闭channel
$channel->close();

//关闭connection
$connection->close();