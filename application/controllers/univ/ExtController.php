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

}