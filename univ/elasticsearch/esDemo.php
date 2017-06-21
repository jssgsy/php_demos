<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/6/21
 * Time: 23:24
 */
//use语句放在require之前
use Elasticsearch\ClientBuilder;
require __DIR__ . '/../../vendor/autoload.php';

echo '<pre>';


//1. 创建客户端(本地先启动es)
$client = ClientBuilder::create()->build();
//可以看一看$client有哪些恨我自己
//var_dump($client);

//在elasticsearch-php中，所有的配置都是通过关联数组完成的

//2. 创建一个索引,注意body属性，body其实就是一个文档(json对象)的属性
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => [
        'field1' => 'field111',
        'field2' => 'field222'
    ]
];

$response = $client->index($params);
print_r($response);


//3. 查询一个文档（包含文档元数据），用get方法
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->get($params);
print_r($response);

//3.1 查询一个文档（不包含文档元数据），用getSource方法即可
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->getSource($params);
print_r($response);


echo '</pre>';
