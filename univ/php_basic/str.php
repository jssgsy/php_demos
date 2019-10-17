<?php

/**
 * 字符串相关的函数
 * strtr ($str, $from, $to)
 * 将str位于from中每个字符替换成to中对应位置的字符
 * 场景：\替换成/
 * 确保from与to长度一样，否则多余的将被舍弃
 */
echo strtr('helyafelfao,worlafeo', 'lo', 'x') . '<br>';