<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/6/4
 * Time: 16:31
 */
//---------------------使用__autoload加载---------------------------
/**
 * @param $class 为了简单起见，约定这里的要加载的class就是定义此class的文件名
 * @return mixed
 */
function __autoload($class){
    echo '__autoload($class)' . ' : 要加载的类为' . $class . '<br>';
    return require_once __DIR__ . '/' . $class . '.php';
}
//当new一个本文件不存放在类时，会尝试去执行本文件的__autoload方法加载需要的类
$a1 = new DemoClass1();


//---------------------使用spl_autoload_register加载---------------------------
//指定普通函数作为__autoload的替换品
function myautoload($class){
    echo 'myautoload($class)' . ' : 要加载的类为' . $class . '<br>';
    return require_once __DIR__ . '/' . $class . '.php';
}
//此时new DemoClass2()时，会尝试去执行本文件的myautoload方法加载需要的类，而不是默认的__autoload方法
spl_autoload_register('myautoload');
$a2 = new DemoClass2();

//指定类的静态方法作为__autoload的替换品
class DemoClass{
    public static function loadClass($class){
        echo '要加载的类为' . $class . '<br>';
        return require_once __DIR__ . '/' . $class . '.php';
    }
}
//先取消注册之前注册的myautoload(spl_autoload_register支持任意数量的加载器)
spl_autoload_unregister('myautoload');
//此时new DemoClass2()时，会尝试去执行本文件的DemoClass类的静态loadClass方法加载需要的类
spl_autoload_register(array('DemoClass', 'loadClass'));
$a3 = new DemoClass3();