# rabbitmq相关的代码都放在此目录下

## [hello world示例](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)

* 启动rabbitmq服务器，需要先安装，在mac下可通过homebrew安装，然后通过rabbitmq-server启动；
* 从远程仓库拉取代码后执行：composer install;
* cd到此目录下；
* 执行 php receive.php;
* 在另一个bash中执行 php send.php;
* 此时注意看输出；