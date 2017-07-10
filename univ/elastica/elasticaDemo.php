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


//下面是常用的搜索操作
/**
 * 搜索返回的对象是Elastica\ResultSet类型
 * 搜索即可以在$index对象下进行，也可以在$type对象下进行。但最终都会上溯到$index对象中
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
 * $results = $resultSet->getResults();//返回的是一个数组，每个元素是一个Elastica\Result对象，即es搜索时返回的结果，包括元字段信息与文档字段信息
    var_dump($results[0]);
 * $hit = $results[0]->getHit();//返回的即es搜索时返回结果中的最底层hits字段的内容,即_index,_type,_id,_version,_source字段的值
    var_dump($hit);
 *
 * $data = $resultSet->getResponse()->getData();//返回的即是es搜索的完整结果,是Elastica/Reponse对象，$resultSet->getTotalHits()与$resultSet->getMaxScore()获取其实就是这里结果的hits.total与hits.max_score字段的值
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

$resultSet = $type->search($boolQuery);
$results = $resultSet->getResults();
echo '命中数为：' . $resultSet->getTotalHits();
foreach ($results as $result) {
    var_dump($result->getData());
}


//当然，复合查询条件也可以通过原生的数组形式，上述查询等价如下：
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





