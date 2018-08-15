<?php
/**
 * 单元测试
 * User: univ <minglu.liu@beibei.com>
 * Date: 2018/8/15 9:56 AM
 * @copyright Beidian Limited. All rights reserved.
 */

/**
 * Class FirstTest
 * 两个约束：
 * 1. 一个类成为测试类的基础：类名以Test结尾；
 * 2. 一个方法成为测试方法的基础：方法名必须心test开头
 */
class FirstTest extends \PHPUnit\Framework\TestCase {

    public function testFn1() {
        $this->assertEquals(true, 1);
    }

}