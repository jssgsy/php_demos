<?php

/**
 * Created by PhpStorm.
 * User: Univ
 * Date: 2017/6/20
 * Time: 19:09
 * 学习CI中的表单验证,与form_validate.php一起使用
 */

class FormValidateController extends CI_Controller {

    public function index() {
        //加载form_validation，如此便不用在页面上加载了
        $this->load->library('form_validation');
        $this->load->view('/univ/form_validate');
    }

    public function validate() {

        //1.加载表单验证类
        $this->load->library('form_validation');

        //2. 设置验证规则，注意，不用去显示获取表单域的值

        /**
         * 为单个域设置验证规则
         * 1.required与min_length[5]必须同时满足;
         * 2.这里还设置了自定义的错误提示信息,如果规则是方法名，则必须调用set_message方法
         */
        $this->form_validation->set_rules('username', '用户名', 'required|min_length[5]',
            array('required' => '必填')
        );
        //这里的{field}即是要验证的表单域username，{param}即是min_length[5]中的5
        $this->form_validation->set_message('min_length', '{field}必须大于{param}个字符');


        //为多个域设置验证规则，这里设置了自定义的错误提示信息
        $rules = array(
            array(
                'field' => 'password',
                'label' => '密码',
                'rules' => 'required'
            ),
            array(
                'field' => 'article',
                'label' => '文章',
                'rules' => 'required',
                'errors' => array(
                    'required' => '这是自定义的错误提示信息',
                ),
            )
        );
        $this->form_validation->set_rules($rules);

        //使用自己的验证回调函数，验证方法前必须加'callback_'前缀
        $this->form_validation->set_rules('sex', '性别', 'required|callback_validate_sex');

        //如果将域名称定义为数组，那么在使用域名称作为参数的辅助函数函数时，必须传递给他们与域名称完全一样的数组名，对这个域名称的验证规则也一样
        $this->form_validation->set_rules('city[]', '城市', 'required');
        $this->form_validation->set_rules('language', '语言', 'required');

        //3. 验证，若是没通过验证，返回到原始的表单提交页面
        if(!$this->form_validation->run() ){
            $this->load->view('/univ/form_validate');
        }

    }

    /**
     * 验证回调函数
     * @param $sex 即表单提交时传递过来的值
     * @return bool
     */
    public function validate_sex($sex) {
        if ($sex == 1) {
            //设置自定义的错误提示信息，第一个参数必须与方法名同名
            $this->form_validation->set_message('validate_sex', '性别必须为女，请重新选择');
            return false;
        }
        //整个方法要么返回false，要么返回true，不要有遗漏
        return true;
    }
}