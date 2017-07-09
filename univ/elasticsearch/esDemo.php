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

//配置主机，一个主机就是一个数组，每个主机配置只有host属性是必须的
//默认为localhost:9200
$hosts = [
    [
        'host' => 'localhost',
        'port' => 9200
    ]
];

$client = ClientBuilder::create()->setHosts($hosts)->build();
//可以看一看$client有哪些成员变量
//var_dump($client);

//在elasticsearch-php中，所有的配置都是通过关联数组完成的

//2. 索引一个文档,注意body属性，body其实就是一个文档(json对象)的属性
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',    //可省略id，此时将自动产生一个唯一的id
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

//检查一个文档是否存在
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];
$response = $client->exists($params);
var_dump($response);

// 查询my_index索引下类型为my_type且field1属性值为field111的文档
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'body' => [
        'query' => [
            'match' => [
                'field1' => 'field111'
            ]
        ]
    ]
];
$response = $client->search($params);
print_r($response);

//删除之前创建的文档，参数与获取一个文档的get方法一样
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];
$response = $client->delete($params);
print_r($response);


//删除my_index索引下的my_type类型?
/*$params = [
    'index' => 'my_index',
    'type' => 'my_type'
];
$response = $client->delete($params);
print_r($response);*/

//删除一个索引,注意，对索引的操作是在indices对象下的delete方法(删除一个文档的delete方法在client对象下)
//显然，只需要指定要删除的index即可
$params = [
    'index' => 'my_index'
];
$response = $client->indices()->delete($params);
print_r($response);

//创建自定义的索引，注意，create在indices对象
$params = [
    'index' => 'my_index',
    'body' => [
        'settings' => [
            'number_of_shards' => 2,
            'number_of_replicas' => 0
        ]
    ]
];
$response = $client->indices()->create($params);
print_r($response);

//获取索引的Setting，相应的有putSettings方法
$params = [
    'index' => 'my_index'
];
//获取多个索引的Settings
/*$params = [
    'index' => ['my_index','shakespeare']
];*/
//不传参数，则为获取所有索引的Setting
$response = $client->indices()->getSettings($params);
print_r($response);


//获取索引的mapping，，相应的有putMapping方法
$params = [
    'index' => 'my_index'
];
//获取多个索引的Mapping
/*$params = [
    'index' => ['my_index','shakespeare']
];*/

//不传参数，则为获取所有索引的Mapping
$response = $client->indices()->getMapping($params);
print_r($response);


//获取索引的alias，，相应的有putAlias方法
$params = [
    'index' => 'my_index'
];
//获取多个索引的alias
/*$params = [
    'index' => ['my_index','shakespeare']
];*/

//不传参数，则为获取所有索引的alias
$response = $client->indices()->getAliases($params);
print_r($response);

echo '</pre>';
