# [elastica官网](http://elastica.io/)
* elastica是es的一个php客户端(elasticsearch-php是es的官方客户端)；

# elastica与elasticsearch-php的区别
在elasticsearch-php中，所有的配置都通过关联数组的形式，而elastica提供了一种更加面向对象的使用方式。具体查看官网说明。

# 基础知识
* 运行示例代码之前先启动es；

# 核心
* 不论是使用elasticsearch-php还是elastica，这两者都只是一个客户端，或者说是es的两者api实现，用来供外界使用，核心还是在于对es本身的理解与使用；
* 因为elastica是用面向对象的方式使用es，因此一个非常重要的观念就是，每一个es中的概念在elastica都有对应的对象表示。如索引，类型，文档等等。需要加倍注意的是，搜索中的概念也是如此，如es中的match查询，在elastica中对应有Match类，term查询对应有Term类，且将相应查询的参数封装到对应的类中。掌握了这个思维方式，学习elastica便会非常轻松。
* 对于复合查询(BoolQuery)，需要注意的是其方法的参数类型，其实就是每个简单查询对应的对象(如match,term等)，具体看elasticaDemo.php中的示例。

# 一个es查询输出格式的示例
```
{
  "took": 2,
  "timed_out": false,
  "_shards": {
    "total": 5,
    "successful": 5,
    "failed": 0
  },
  "hits": {
    "total": 10,
    "max_score": 1,
    "hits": [
      {
        "_index": "gb",
        "_type": "tweet",
        "_id": "9",
        "_score": 1,
        "_source": {
          "date": "2014-09-19",
          "name": "Mary Jones",
          "tweet": "Geo-location aggregations are really cool",
          "user_id": 2
        }
      },
      {
        "_index": "gb",
        "_type": "tweet",
        "_id": "5",
        "_score": 1,
        "_source": {
          ...
        }
      }
    ]
  }
}
```
![elastica与原生es元素对应关系](https://github.com/jssgsy/php_demos/raw/master/pics/elastica与原生es元素对应关系.png)

# 面向对象的查询
* elastica中，\Elastica\Index中有search方法，\Elastica\Type中也有search方法，search方法的参数是原生的es搜索数组或者\Elastica\Query对象，但这两者`search方法内部最终都是委托给了Search对象的search方法`；
* es的DSL中，query是顶层的搜索字段，复杂的查询也都是在此query字段下，即\Elastica\Query可以封装复杂的查询，因此可以说\Elastica\Query在elastica中是最重要的查询对象；
* 分页的from与size，排序的sort都是与顶层的query同级的搜索字段，但在elastica中，`这些条件的设置都被安放在\Elastica\Query对象下`，如setFrom,setSize,setSort,addSort等方法；
* \Elastica\Query对象没有setBool(Query)方法，要设置bool查询在query字段下，利用\Elastica\Query对象的`setQuery`方法

## 搜索的最佳实践
* 最核心是理解原生es的查询；
* 对照原生es的DSL的查询语法，将所有查询逐层封装在\Elastica\Query对象中(如must,should等)，并将其作为search方法的入参；


![elastica查询对应元素](https://github.com/jssgsy/php_demos/raw/master/pics/elastica查询对应元素.png)

# 关于排序
elastica中没有为排序封装对象，需要传入原生的搜索数组。
```php
$query = new \Elastica\Query();
$query->setSort([
    // 先按date字段升序，再按user_id字段降序
    "date" => ["order" => "asc"],
    "user_id" => ["order" => "desc"]
]);
```
* setSort方法
设置排序字段，如果有多处调用setSort方法，则后面的setSort方法会覆盖掉之前的setSort方法设置的排序；
* addSort方法
与setSort方法不同，addSort会新增排序字段，不会覆盖掉之前addSort方法设置的排序字段；







