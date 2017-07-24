# 说明
* 测试ci中对数据库的基本操作

# 核心
* 数据库配置文件为`application/config/database.php`

## 数据库相关核心类

* $this->load：CI_Loader;
* $this->db:
    * CI_DB_driver：普通查询(自己写sql语句)；
    * CI_DB_query_builder：查询构造器；
* CI_DB_result：查询结果集；
