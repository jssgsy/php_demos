<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/8/22
 * Time: 20:23
 *
 * 继承自扩展CI_Controller的My_Controller
 * 仅是测试My_Controller的正确性
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
         */
        $this->DemoModel->func1();
    }

}