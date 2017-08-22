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

}