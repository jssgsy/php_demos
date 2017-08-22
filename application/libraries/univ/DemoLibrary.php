<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/8/22
 * Time: 20:29
 *
 * 新增library
 * library就是一个普通的类，除了放在application/library目录及其子目录下外，不用加任何其它约束。
 */

class DemoLibrary {

    public function demo_func() {
        echo 'DemoLibrary的demo_func方法被调用了' . '<br>';
    }

}