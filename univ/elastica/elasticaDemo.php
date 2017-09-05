<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/7/9
 * Time: 14:13
 * 运行本文件代码前，先启动es
 */

require_once __DIR__ . '/../../vendor/autoload.php';

//1. 连接到es集群上
//默认就是localhost:9200
$hosts = [
    'host' => 'localhost',
    'port' => 9200
];
$elasticaClient = new \Elastica\Client($hosts);

//下面是索引操作：获得一个索引,关于索引有哪些操作，直接看$index有哪些方法即可，一目了然
$index = $elasticaClient->getIndex('gb');
var_dump($index);
/**
 *  $name = $index->getName();//获取索引的名字,这里即gb
 *  $alias = $index->getAliases();//获得索引的别名
 *  $mapping = $index->getMapping();//获得索引所有类型(type)的mapping
 *  $setting = $index->getSettings();//获得索引的setting
 *  $exist = $index->exists();//判断索引是否存放
 */


//下面是类型操作：获取某个索引下的类型,关于类型有哪些操作，直接看$type有哪些方法即可，一目了然
$type = $index->getType('tweet');
var_dump($type);
/**
 *  $name = $type->getName();//获取类型的名字,这里即tweet
 *  $index = $type->getIndex();//获取此类型所属的索引,这里即gb,注意，是一个索引对象
 *  $mapping = $type->getMapping();//获取此类型的mapping
 *  $exist = $type->exists();//判断此类型是否存在
*/

//下面是文档操作，在类型中根据id获取某个文档，关于文档有哪些操作，直接看$document有哪些方法即可，一目了然
$document = $type->getDocument(9);
var_dump($document);

//获取文档的字段(即_source)信息，返回的是一个数组.相应的有setData方法
$data = $document->getData();
var_dump($data);

//获取文档的元字段信息，即_index,_type,_id,_version四个字段的值
$params = $document->getParams();
var_dump($params);
/**
 * $index = $document->getIndex();//获取此文档所属的索引的名字,这里即gb，注意，返回的直接是字符串
 * $type = $document->getType();//获取此文档所属的类型的名字,这里即tweet，注意，返回的直接是字符串
 * $id = $document->getId();//获取此文档的id，这里即9
 */

/**
 * 创建文档，用addDocument方法
 * 以在gb/tweet下新建文档为例
 */
$doc_id = 5;
//注意，这里特意没有插入tweet字段的值,所以插入的文档中没有tweet字段
$insert_data = [
    'user_id' => 22,
    'name' => 'unsssiv',
    'date' => '2017-07-25'
];
$doc = new \Elastica\Document($doc_id, $insert_data);
/*
 * 如果被插入文档的$doc_id已经存在，则会被覆盖掉
 * addDocument方法返回的是一个\Elastica\Response对象,其常用的方法有：
 * isOk():文档是否被插入成功；
 * 其它方法查看源码即可；
 */
$insert_result = $type->addDocument($doc);
if ($insert_result->isOk()) {
    echo '文档插入成功';
}
var_dump($insert_result);

/**
 * 批量创建文档，用addDocuments（复数形式）
 */
$docs = [];
$docs[] = new \Elastica\Document(21, ['user_id' => 22, 'name' => 'unsssiv']);
$docs[] = new \Elastica\Document(22, ['user_id' => 23, 'name' => 'fcsu', 'date' => '2017-07-25','age' => 27]);
$docs[] = new \Elastica\Document(24, ['user_id' => 24, 'city' => 'hangzhou', 'interest' => 'movie']);

/**
 * 与addDocument方法一样，如果被插入文档的$doc_id已经存在，则会被覆盖掉
 * addDocuments方法的返回类型为\Elastica\Bulk\ResponseSet，其常用的方法有：
 * isOk():文档是否被插入成功；
 * 其它方法查看源码即可；
 */
$insert_result = $type->addDocuments($docs);
if ($insert_result->isOk()) {
    echo '文档批量插入成功' . '<br>';
}
//var_dump($insert_result);

/**
 * 更新文档，用updateDocument方法，与addDocument方法的用法一模一样
 */
$doc_id = 5;
/**
 * 假设要更新的id为$doc_id的文档中的字段有十个，这里的$update_data中只给出了其中的三个，则更新后文档中仍然还是十个字段。
 * 即es中的更新其实是按需更新。当然，如果$update_data给出了原来文档中没有的字段，则该字段将被添加。
 */
$update_data = [
    'user_id' => 22,
    'name' => 'unsssiv',
    'date' => '2016-07-25',
    'sex' => 'man'
];
/**
 * 注意：如果被更新的$doc_id不存在，则会抛出异常
 * 返回的是\Elastica\Response对象，常见的方法有：
 * isOk：:文档是否被更新成功；
 */
$doc = new \Elastica\Document($doc_id, $update_data);
$update_result = $type->updateDocument($doc);
if ($update_result->isOk()) {
    echo '更新文档成功' . '<br>';
}
var_dump($update_result);

/**
 * 批量创建文档,用updateDocuments方法
 * 特别注意：只要有一个文档没有更新成功，便会抛出异常，但并不是原子操作，能更新成功的会更新成功
 * addDocuments方法的返回类型为\Elastica\Bulk\ResponseSet，常用的方法有：
 * isOK()：文档是否被更新成功；
 *
 */
$docs = [];
$docs[] = new \Elastica\Document(21, ['user_id' => 22, 'name' => 'aaaaa']);
//没有id为220000的文档，但id为21与24的文档都会被更新
$docs[] = new \Elastica\Document(220000, ['user_id' => 23, 'name' => 'bbbb', 'date' => '2017-07-25','age' => 27]);
$docs[] = new \Elastica\Document(24, ['user_id' => 24, 'city' => 'hubei', 'interest' => 'movie']);
try{
    $update_result = $type->updateDocuments($docs);
    if ($update_result->isOk()) {
        echo '批量更新文档成功' . '<br>';
    }
}catch (Exception $ex) {
    var_dump($ex);
}


//下面是常用的搜索操作
/**
 * 搜索返回的对象是Elastica\ResultSet类型
 * 搜索即可以在$index对象下进行，也可以在$type对象下进行。但最终都会上溯到Elastica/Search对象中,Search是真正的搜索对象
 */
//搜索此索引下的所有文档，$resultSet包含了搜索结果的所有信息，过于庞大
//$resultSet = $index->search();

//搜索此类型下的所有文档，$resultSet包含了搜索结果的所有信息，过于庞大
$resultSet = $type->search();

//命中数，即es搜索结果中hits.total字段的值
$totalHits = $resultSet->getTotalHits();
var_dump($totalHits);
/**
 * $maxScore = $resultSet->getMaxScore();//得分，即es搜索结果中hits.max_score字段的值;
 * $results = $resultSet->getResults();//返回的是一个数组，每个元素是一个Elastica\Result对象，即es搜索时返回结果中hits.hits数组下的一个对象，包括元字段信息与文档字段信息
    var_dump($results[0]);
 * $hit = $results[0]->getHit();//返回的即es搜索时返回结果中的最底层hits字段的内容,即_index,_type,_id,_version,_source字段的值
    var_dump($hit);
 *
 * $data = $resultSet->getResponse()->getData();//getResponse返回的是Elastica\Response对象，getData返回的即是es搜索的完整结果,数组形式，$resultSet->getTotalHits()与$resultSet->getMaxScore()获取其实就是这里结果的hits.total与hits.max_score字段的值
    var_dump($data);
 */
$results = $resultSet->getResults();
//返回的即es搜索时返回结果中的最底层hits字段中_source字段的内容，重要!
$data = $results[0]->getData();
var_dump($data);

//既然使用elastica，那当然要用面向对象的方式，这里的核心是要理解，每个es中的概念在elastica中都由对应的对象表示

//简单查询：以match查询为例
$match = new \Elastica\Query\Match();
$match->setField('name', 'Jones');

/**
 * 注意，不要被search方法签名中参数的类型吓到了，其实参数不仅仅可以为Query类型，
 * AbstractQuery也可以，其实还有许多其它类型也可以，可追踪源码到Elastica/Query的create方法查看
 */
$resultSet = $type->search($match);

//用原生es数组形式表示查询，如上面的面向对象的查询等价于如下形式
/*$params = [
    "query" => [
        "match" => [
            "name" => "Jones"
        ]
    ]
];*/
$resultSet = $type->search($match);
echo '命中数为：' . $resultSet->getTotalHits();
$results = $resultSet->getResults();
foreach ($results as $result) {
    $data = $result->getData();
    var_dump($data);
}
/**
 * $matchAll = new \Elastica\Query\MatchAll();
    $resultSet = $type->search($matchAll);//匹配此类型下的所有文档
 * $term = new \Elastica\Query\Term();
    $resultSet = $type->search($term);//term查询
 * 诸如此类
 */

/**
 * 复合查询
 * 重点是理解方法BoolQuery对象的方法(如addShould)的参数类型，addShould,addMust,addMustNot等方法参数都是一个普通查询，如match,term等
 */
//Bool已被废弃（From PHP7 bool is reserved word and this class will be removed in further Elastica releases），使用BoolQuery类
//$bool = new \Elastica\Query\Bool();

// 最顶层的query查询字段
$query = new \Elastica\Query();

$boolQuery = new \Elastica\Query\BoolQuery();

$match = new \Elastica\Query\Match();
$match->setField('name', 'Jones');
//在复合查询(bool)下新增一个must查询
$boolQuery->addMust($match);

$match2 = new \Elastica\Query\Match();
$match2->setField('tweet', 'powerful');
//在复合查询(bool)下新增一个must查询
$boolQuery->addMust($match2);

$term = new \Elastica\Query\Term();
$term->setTerm('date', '2014-09-17');
//在复合查询(bool)下新增一个should查询
$boolQuery->addShould($term);

// 重要的方法，可以放置其它查询字段，如bool查询
$query->setQuery($boolQuery);

// 分页
/*$query->setFrom(1);
$query->setSize(2);*/

// 排序，注意，排序必须传入原生的es搜索数组，没有sort对象
// setSort，如果有多个sortSort方法，则后面设置的排序字段与规则会覆盖之前的
$query->setSort([
    // 先按date字段升序，再按user_id字段降序
    "date" => ["order" => "asc"],
    "user_id" => ["order" => "desc"]
]);

// 上述setSort等价于如下代码
// addSort，与setSort的不同之处在于，addSort会新增排序字段与规则，而不是覆盖掉之前的排序字段与规则
$query->addSort([
    "date" => ["order" => "asc"]
]);
$query->addSort([
    "user_id" => ["order" => "desc"]
]);

$resultSet = $type->search($query);
$results = $resultSet->getResults();
echo '命中数为：' . $resultSet->getTotalHits();
foreach ($results as $result) {
    var_dump($result->getData());
}


//当然，复合查询条件也可以通过原生的数组形式：
//注意，这里的示例中must查询下有两个简单查询，注意其写法
/*$params = [
    "query" => [
        "bool" => [
            "must" => [
                'match' => [
                    'name' => 'Jones'
                ],
                'match' => [
                    'tweet' => 'powerful'
                ]
            ],
            'should' => [
                'term' => [
                    "date" => "2014-09-17"
                ]
            ]
        ]
    ]
];
$resultSet = $type->search($params);*/



