
<?php $this->load->helper('form');?>
<p>学习form的辅助函数</p>
<!--小结，租用form的辅助函数时，一般只需要给最小的属性集合即可，对于radio,checkbox,select可默认选中的元素，不用过分依赖于第一个属性，因为默认选中是由第三个参数控制的-->
<?php
    //用来定义form标签的属性集合
    $form_attr = [
        'method' => 'post',
        'id' => 'form_id',
        'class' => 'form_class',
    ];
    echo form_open('http://localhost:8080/php_demos/index.php/univ/formcontroller/form_test', $form_attr);

    //input, 用来定义input标签的属性集合
    $data = [
        'id' => 'text_id',
        'class' => 'text_class',
        'name' => 'text_name',
    ];
    echo form_input($data) . '<br>';

    //password, 用来定义password标签的属性集合
    $data = [
        'id' => 'password_id',
        'class' => 'password_class',
        'name' => 'password_name',
    ];
    echo  form_password($data) . '<br>';

    //textarea, 用来定义textarea标签的属性集合
    $data = [
        'name' => 'textarea_name',
    ];
    echo form_textarea($data). '<br>';

    //radio, 用来定义radio标签的属性集合
    $data = [
        'name' => 'radio_name',
//        'value' => 'radio_man'
    ];
    //第三个参数默认选中
    echo form_radio($data, 'radio_man', true) . '男';
    $data = [
        'name' => 'radio_name',
        'value' => 'radio_women'
    ];
    echo form_radio($data) . '女' . '<br>';

    /**
     * checkbox, 用来定义checkbox标签的属性集合
     * 注意，因为checkbox需要能向后台传递多个值，有两种处理方法，
     * 一是像如下所示，设置name的值为一个数组，
     * 二是在表单提交时利用js将所有选中的值拼接起来然后传递给前台
     */
    $data = [
        'name' => 'checkbox_name[]',
        'value' => 'checkbox_wuhan'
    ];
    echo form_checkbox($data) . '武汉';
    $data = [
        'name' => 'checkbox_name[]',
        'value' => 'checkbox_hangzhou'
    ];
    echo form_checkbox($data) . '杭州';
    $data = [
        'name' => 'checkbox_name[]',
        'value' => 'checkbox_shanghai'
    ];
    echo form_checkbox($data) . '上海' . '<br>';

    $data = [
        'name' => 'checkbox_name[]',
        //'value' => 'checkbox_shenzhen'
    ];

    /**
     * 第二个参数指定此复选框的值(一般而言可能利用这种方式而不是利用$data)，第三个参数表示默认选中
     */
    echo form_checkbox($data, 'checkbox_shenzhen', true) . '深圳' . '<br>';

    //select(dropdown), 用来定义select标签的属性集合
    $data = [
        'name' => 'select_name',
        'class' => 'select_class',
        'id' => 'select_id',
    ];
    //如何默认选中？传第三个参数，如下所示
    $options = [
        'option1' => 'option1_text',
        'option2' => 'option2_text',
        'option3' => 'option3_text',
        'option4' => 'option4_text',
        'option5' => 'option5_text',
    ];
    /**
     * 第二个参数用来构造其下的option标签，key为option标签的name指定的值，value为显示在option标签之间的值
     * 第三个参数表示默认选中哪一项，
     */
    echo form_dropdown($data, $options, 'option3') . '<br>';

    //hidden, hidden一般只需要一个name属性与value属性，因此ci给予了简便写法
    echo form_hidden('hidden_name', 123);

    //submit, 用来定义submit标签的属性集合
    $data = [
        'value' => '提交',
    ];
    echo form_submit($data);

    echo form_close();
?>

