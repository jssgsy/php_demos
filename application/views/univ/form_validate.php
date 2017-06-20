<!--
1. 如果要想值能回传，为每个表单域赋值为<?php echo set_value('field_name'); ?>
2. 想正确的显示错误提示信息，在每个表单域周围(位置可随着需求变)放置<?php echo form_error('field_name'); ?>
3. 如果想将错误提示信息以列表的信息展现(而不是像上面第2点一样在每个表单域周围显示)，可在表单周围放置<?php echo validation_errors(); ?>
-->
<form action="http://localhost:8080/php_demos/index.php/univ/FormValidateController/validate" method="post">
    用户名：<input type="text" name="username" value="<?php echo set_value('username'); ?>"/><br>
    <?php echo form_error('username'); ?>

    密码：<input type="password" name="password" value="<?php echo set_value('password'); ?>" /><br>
    <?php echo form_error('password'); ?>

    文章：<textarea name="article"><?php echo set_value('article'); ?></textarea><br>
    <?php echo form_error('article'); ?>

    <!--注意单选框，复选框与下拉框回填的写法-->
    男：<input type="radio" name="sex" value="1" <?php echo set_radio('sex', 1); ?>/><br>
    女：<input type="radio" name="sex" value="2" <?php echo set_radio('sex', 2); ?> /><br>
    <?php echo form_error('sex'); ?>

    湖北：<input type="checkbox" name="city[]" value="1" <?php echo set_checkbox('city[]', 1); ?> /><br>
    浙江：<input type="checkbox" name="city[]" value="2" <?php echo set_checkbox('city[]', 2); ?> /><br>
    广东：<input type="checkbox" name="city[]" value="3" <?php echo set_checkbox('city[]', 3); ?> /><br>
    <!--注意，这里是[]-->
    <?php echo form_error('city[]'); ?>

    下拉框：
    <select name="language">
        <option value="" <?php echo set_select('language', ''); ?>>null</option>
        <option value="0" <?php echo set_select('language', 0); ?> >js</option>
        <option value="1" <?php echo set_select('language', 1); ?> >php</option>
        <option value="2" <?php echo set_select('language', 2); ?> >java</option>
    </select>
    <?php echo form_error('language'); ?>

    <input type="submit" value="submit"/>

</form>
<!--
1. 普通文本框(text,password,text_area)回填用set_value方法；
2. 单选框用回填用set_radio方法，注意是用来radio标签的空白属性中；
3. 复选框用回填用set_checkbox方法，注意是用来checkbox标签的空白属性中；
4. 下拉框用回填用set_select方法，注意是用来select标签的空白属性中；
-->



