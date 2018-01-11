<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/8/22
 * Time: 20:01
 *
 * 扩展CI的核心类(system/core)
 *  1. 类在定义时必须继承自要扩展的核心类(如这里的CI_Controller);
 *  2. 新类名和文件名必须为 MY_ + class_name，MY_由config.php文件中subclass_prefix属性指定，class_name为要继承的核心类的文件名
 *  3. 只需这两步就足够了，不用做其它任何操作，在CI启动时会自动加载(先加载核心类，然后加载扩展类)
 * 注意：
 *  1. 扩展后，其它的业务控制器可继承自My_Controler
 *  2. 扩展的核心类必须放在application/core目录下，不能是其子目录；
 *
 *
 */

/**
 * Class My_Controller
 * @property CI_Loader $load
 * @property CI_Config $config
 * @property CI_Input $input
 * @property CI_Output $output
 */
class My_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        /**
         * 与java不同，php中不会在构造函数的第一行调用父类的无参构造函数，因此
         * 如果这里还有其它操作，则别忘了上面调用父类的构造函数；
         */
    }

    /**
     * 这是扩展的方法
     */
    public function my_fun() {
        echo 'My_Controller的my_fun方法被调用了' . '<br>';
    }

}