# [elastica官网](http://elastica.io/)
* elastica是es的一个php客户端(elasticsearch-php是es的官方客户端)；

# elastica与elasticsearch-php的区别
在elasticsearch-php中，所有的配置都通过关联数组的形式，而elastica提供了一种更加面向对象的使用方式。具体查看官网说明。

# 基础知识
* 运行示例代码之前先启动es；

# 核心
* 不论是使用elasticsearch-php还是elastica，这两者都只是一个客户端，或者说是es的两者api实现，用来供外界使用，核心还是在于对es本身的理解与使用；
* 因为elastica是用面向对象的方式使用es，因此一个非常重要的观念就是，每一个es中的概念在elastica都有对应的对象表示。如索引，类型，文档等等。需要加倍注意的是，搜索中的概念也是如此，如es中的match查询，在elastica中对应有Match类，term查询对应有Term类，且将相应查询的参数封装到对应的类中。掌握了这个思维方式，学习elastica便会非常轻松。