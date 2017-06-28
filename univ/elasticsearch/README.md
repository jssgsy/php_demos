# [elasticsearch-php官网](http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/5.0/index.html)
* elasticsearch-php是es官方的php客户端；
* 打开vendor目录，发现elasticsearch/elasticsearch依赖于guzzlehttp/ringphp与guzzlehttp/streams;
* 最好的学习资料就是vendor/elasticsearch/elasticsearch中的README.MD;

# 基础知识

* 运行示例代码之前先启动es；
* 在elasticsearch-php中，所有的配置都是通过关联数组表示；
* 对索引的增删改查等操作利用client对象下的indices对象，对文档的增删改查等操作直接利用client对象；