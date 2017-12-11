# 目录结构
* rabbitmq目录存放与rabbitmq相关的所有代码；
* vendor目录、composer.lock文件由composer自动生成；


# 补充
* 访问ci时，注意使用环境配置的端口号，如使用phpStrom时设置了端口为8080，则每个url后需要手动添加8080端口，否则访问失败

# CI基础知识
CI的入口是index.php，因此通常的url请求的形式为*example.com/index.php/xxx*，进入到index.php，则CI便被启动了。
如：http://localhost:8080/php_demos/index.php/univ/FormController/form_test

# CI与composer集成
* config/config.php配置文件中提供了composer_autoload用来使用composer的自动加载功能；
* 设置composer_autoload配置项为true，则会将application/vendor/autoload.php作为composer的自动加载文件；
* 一般vendor目录放在项目的根目录下，因此设置composer_autoload的值为真实vendor/autoload.php的路径即可；