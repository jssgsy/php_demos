<h2>这是测试页，供其它代码跳转使用。</h2>

<p>从form.php页面传递过来的表单参数如下：</p>
<?php
//    list($text_name, $password_name, $textarea_name, $radio_name, $checkbox_name, $select_name, $hidden_name) = $form_data;
    echo '文本框的值：' . $text_name . '<br>';
    echo '密码框的值：' . $password_name . '<br>';
    echo 'textarea框的值：' . $textarea_name . '<br>';
    echo '单选框的值：' . $radio_name . '<br>';
    echo '多选框的值：' .  '<br>';
    foreach ($checkbox_name as $key => $value) {
        echo $key . " : " . $value . '<br>';
    }
    echo '下拉框的值：' . $select_name . '<br>';
    echo '隐藏域的值：' . $hidden_name . '<br>';
?>
