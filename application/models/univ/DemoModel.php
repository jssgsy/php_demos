<?php
/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/8/22
 * Time: 21:11
 *
 * 新增model
 *  1. 必须继承自CI_Model
 *  2. 类名与文件名保持一致
 *
 * 注意，调用model时，区分大小写，见ExtController中的demo_model方法
 */

class DemoModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        // 如果在其它代码，不要忘了在上面调用父类的构造函数
    }

    public function func1() {
        echo '模型类DemoModel的func1方法被调用了' . '<br>';
    }


}