<?php

/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/7/11
 * Time: 09:15
 *
 * 运行下面代码之前保证：
 *  php的mysqli扩展已经装好；
 *  mysql(mariadb)已经启动;
 *  用户：test,密码：123，参见database.php文件
 *
 * result()方法没有找到数据则返回空数组；
 *
 */
class DBController extends CI_Controller {

    //最好在表名两边增加空格，避免因代码纰漏报错，如'select *  from' . $this->table_name
    private $table_name = ' student ';

    public function __construct() {

        //千万不要忘了调用父类的构造函数，否则ci框架没法接入
        parent::__construct();

        /**
         * 加载数据库配置文件，连接数据库
         * database()方法内部会给CI_Controller对象添加db属性(具体是哪个类的对象是动态变化的，不用过于纠结，只需要知道与数据库打交道的是此db对象即可)
         */
        $this->load->database();
    }

    public function index() {
        /**
         * 普通查询
         * query等方法位于CI_DB_driver，此时CI_DB继承自CI_DB_driver
         */
        $this->get_all();
        $this->get_by_id(1);
        $this->get_by_id_and_name(1, 'univ');

        /**
         * 查询构造器
         * get等方法都位于CI_DB_query_builder类中，此时CI_DB继承自CI_DB_query_builder
         */
        $result = $this->db->get($this->table_name)->result();
        $this->show_result($result);
    }

    private function get_all() {
        $sql = 'select * from' . $this->table_name;
        //result()方法以数组对象的形式返回，即数组中的每个元素都是对象,找不到则返回空数组
        $result = $this->db->query($sql)->result();
        $this->show_result($result);

    }

    private function get_by_id($id = 1){
        $sql = 'SELECT * FROM ' . $this->table_name . 'where id = ?';
        $result = $this->db->query($sql, [$id])->result();
        $this->show_result($result);
    }

    /**
     * 演示占位符的用法
     * @param $id
     * @param $name
     */
    private function get_by_id_and_name($id, $name) {
        $sql = 'select * from' . $this->table_name . 'where id = ? and name = ?';
        //占位符的用法
        $result = $this->db->query($sql,[$id, $name])->result();
        $this->show_result($result);
    }

    /**
     * 方便输出
     * @param $arr
     */
    private function show_result($arr){
        echo '<br>';
        foreach ($arr as $row) {
            echo $row->id . '    ' . $row->stu_id . '    ' . $row->name . '<br>';
        }
    }
}