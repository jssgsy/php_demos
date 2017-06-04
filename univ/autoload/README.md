# php中类的自动加载

* __autoload():在php7.2中已经被废弃
* spl_autoload_call():尝试调用所有已注册的__autoload()函数来装载请求类
* spl_autoload_register: 注册给定的函数作为 __autoload 的实现

> 尽管 __autoload() 函数也能自动加载类和接口，但更建议使用 spl_autoload_register() 函数。 spl_autoload_register() 提供了一种更加灵活的方式来实现类的自动加载（同一个应用中，可以支持任意数量的加载器，比如第三方库中的）。因此，不再建议使用 __autoload() 函数，在以后的版本中它可能被弃用
                      
# 补充
发现执行index.php后，ci中的某些文件内容竟然发生了变化，忽略即可。不要加入到版本控制中。