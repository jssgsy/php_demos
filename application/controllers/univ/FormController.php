<?php
/**
 * Created by PhpStorm.
 * User: minglu.liu
 * Date: 2017/4/18
 * Time: 19:31
 */

//namespace com\univ\controller;


class FormController extends \CI_Controller{

    public function index(){
        $this->load->view("univ/form");
    }

    public function form_test(){
        //获取页面表单中的值
        $text_name = $this->input->post('text_name');
        $password_name = $this->input->post('password_name');
        $textarea_name = $this->input->post('textarea_name');
        $radio_name = $this->input->post('radio_name');
        $checkbox_name = $this->input->post('checkbox_name');
        $select_name = $this->input->post('select_name');
        $hidden_name = $this->input->post('hidden_name');
        $form_data = [
            'text_name' => $text_name,
            'password_name' => $password_name,
            'textarea_name' => $textarea_name,
            'radio_name' => $radio_name,
            'checkbox_name' => $checkbox_name,
            'select_name' => $select_name,
            'hidden_name' => $hidden_name,
        ];
        $this->load->view("univ/home", $form_data);
    }
}