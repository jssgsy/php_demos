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
 *
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

        //查询辅助函数
        $this->query_util();

        /**
         * 查询构造器
         * get等方法都位于CI_DB_query_builder类中，此时CI_DB继承自CI_DB_query_builder
         */
        $result = $this->db->get($this->table_name)->result();
        $this->show_result($result);
    }

    /**
     * 演示result方法:
     *  result()方法以数组对象的形式返回，即数组中的每个元素都是对象,找不到则返回空数组
     * 类似的方法有result_array:返回数组中的每个元素还是数组
     */
    private function get_all() {
        $sql = 'select * from' . $this->table_name;
        $result = $this->db->query($sql)->result();
        $this->show_result($result);
        echo '刚被执行的sql语句为：' . $this->db->last_query() . '<br>';

        /**
         * 可以给result方法传递一个参数，这个字符串参数代表你想要把每个结果转换成某个类的类名，这个类必须已经加载
         * 如下表示将查询的结果集中的每条记录转换成student类，一般不这样用
         * 注意，下面的代码不能运行，因为student类还没有被加载
         */
        /*$result = $this->db->query($sql)->result('student');
        foreach ($result as $stu) {
            $stu->id;
            $stu->stu_id;
            $stu->name;
        }*/

    }

    /**
     * 演示CI_DB_result类的常用方法
     *  result();
     *  result_array();
     *  num_rows()：返回查询结果的行数
     *  num_fields()：该方法返回查询结果的字段数（列数）
     *  list_fields()：返回查询的列名，索引数组形式
     *  first_row();
     *  last_row();
     *  field_data()：返回查询的字段的元数据，一般不用
     */
    private function get_by_id($id = 1){
        $sql = 'SELECT * FROM ' . $this->table_name . 'where id = ?';
        echo '返回查询结果的行数为：' . $this->db->query($sql, [$id])->num_rows() . '<br>';
        echo '返回查询结果的字段数为：' . $this->db->query($sql, [$id])->num_fields() . '<br>';
        var_dump($this->db->query($sql, [$id])->list_fields());
        var_dump($this->db->query($sql, [$id])->field_data());
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
     * 查询辅助函数
     * insert_id():当执行 INSERT 语句时，这个方法返回新插入行的ID;
     * affected_rows():当执行 INSERT、UPDATE 等写类型的语句时，这个方法返回受影响的行数
     * last_query():返回上一次执行的查询语句（是查询语句，不是结果）,注意，此方法可用在任意查询语句后，有用
     * count_all($table_name)：返回数据表的总行数；
     * platform()：输出你正在使用的数据库平台（MySQL，MS SQL，Postgres 等）；
     * version()：输出你正在使用的数据库版本
     */
    private function query_util() {
        $sql = 'insert into ' . $this->table_name . '(stu_id, name)values(?,?)';
        $this->db->query($sql, [100001, '李四']);
        echo '刚插入的记录的id为：' . $this->db->insert_id() . '<br>';
        echo '刚插入的记录被影响的行数为：' . $this->db->affected_rows() . '<br>';
        echo '刚被执行的sql语句为：' . $this->db->last_query() . '<br>';
        echo '表 '. $this->table_name . '中的总记录数为：' . $this->db->count_all($this->table_name) . '<br>';

        echo '使用的数据库平台为：' . $this->db->platform() . '<br>';
        echo '使用的数据库版本为：' . $this->db->version() . '<br>';

        /**
         * insert_string():简化了 INSERT 语句的书写，它返回一个正确格式化的 INSERT 语句;
         * update_string():简化了 UPDATE 语句的书写，它返回一个正确格式化的 UPDATE 语句;
         */
        $insert_data = ['stu_id' => 100003, 'name' => '五五'];
        $insert_sql = $this->db->insert_string($this->table_name, $insert_data);
        echo 'insert_string()返回的的sql语句为：' . $insert_sql . '<br>';
        $this->db->query($insert_sql);
        echo '刚插入的记录的id为：' . $this->db->insert_id() . '<br>';

        $update_data = ['stu_id' => 100003, 'name' => '五五五五'];
        $where = 'id = 8';
        $update_sql = $this->db->update_string($this->table_name, $update_data, $where);
        echo 'update_string()返回的的sql语句为：' . $update_sql . '<br>';
        $this->db->query($update_sql);
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