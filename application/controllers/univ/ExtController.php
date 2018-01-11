<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/8/22
 * Time: 20:23
 *
 * 继承自扩展CI_Controller的My_Controller
 * 测试CI的各种小功能
 */

class ExtController extends My_Controller {

    /**
     * my_fun()是继承自My_Controller的方法
     */
    public function index() {
        $this->my_fun();
    }

    /**
     * 测试如何使用新增的library
     * library被加载后会成为超级对象CI_Controller的成员变量
     */
    public function demo_library() {
        $this->load->library('univ/DemoLibrary');
        /**
         * 注意，library的调用只能用全部的小写，跟踪CI_Loader的library源码
         */
        $this->demolibrary->demo_func();

    }

    /**
     * 测试如何使用新增的helper
     */
    public function demo_helper() {
        // 这样加载也可以，$this->load->helper('univ')，helper内部会处理
        $this->load->helper('univ_helper');

        /**
         * 下面这句代码是错误的
         * 不同于library，helper中都是一个函数，且是全局函数，因此加载后直接调用其中的方法即可
         */
        // $this->univ_helper->fun1();
        fun1();
    }

    /**
     *
     */
    public function demo_model() {
        $this->load->model('univ/DemoModel');

        /**
         * 与library不同，加载后的model调用区分大小写，$this->demomodel是错误的
         * 如果用的是$this->load->model('univ/DemoModel'),则用法为$this->DemoModel；
         * 如果用的是$this->load->model('univ/demomodel'),则用法为$this->demomodel；
         * 最保险的方法是，$this->load->model('文件名')，即文件名大写这里就大写，文件名小写这里就小写，这样就可以避免不同系统下区分大小写的坑
         * 其实是，model方法内部会将model方法的参数(去掉其中的路径)直接赋值给$CI；
         */
        $this->DemoModel->func1();
    }

    /**
     * 测试CI集成composer，使用composer的自动加载
     * 此时不用require进LengthException所在的文件；
     */
    public function demo_composer() {
        new \React\Promise\Exception\LengthException();
    }

    public function demo_config() {
        /**
         * 此时会同时加载两个文件：
         *  1. application/config/univ/myconfig.php
         *  2. application/config/ENVIRONMENT/univ/myconfig.php
         * 即当前环境下对应的配置文件也会被加载
         */
        $this->load->config('univ/myconfig');
        $name = $this->config->item('univ_name');
        $name_dev = $this->config->item('univ_name_dev');
        echo '全局配置文件config/univ/myconfig.php中的univ_name项为： ' . $name . '<br>';
        echo '局部配置文件config/development/univ/myconfig.php中的univ_name_dev项为： ' . $name_dev . '<br>';
    }

    /**
     * CI返回json数据
     * 注意，因为在MyHook.php文件中有echo输出，实验观察结果时注意先屏蔽一下
     */
    public function json_return() {
        $this->output->set_content_type('application/json');
        $arr = ['name' => 'univ', 'age' => 24];
        // 输出
        $this->output->set_output(json_encode($arr));

        // 或者直接echo json_encode($arr)
    }

}