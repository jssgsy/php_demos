<?php
/**
 * php7新特性测试
 * User: univ <minglu.liu@beibei.com>
 * Date: 2018/8/29 3:00 PM
 * @copyright Beidian Limited. All rights reserved.
 */


class NewFeatureTest extends \PHPUnit\Framework\TestCase {

    /**
     * 测试返回类型
     */
    public function testReturnType() {
        $this->assertEquals(3, $this->add(1, 2));
    }

    /**
     * 测试匿名类，注意语法即可
     * 可以加参数
     */
    public function testAnonymousClss() {
        $return = $this->setA(new class('aaa') extends A {

        });
        $this->assertEquals('aaa', $return);
    }
    /**
     * php7中参数与返回值都可以加返回类型
     * @param int ...$arr
     * @return int
     */
    public function add(int ...$arr) :int {
        return array_sum($arr);
    }

    public function setA(A $a) :string {
        return $a->fn();
    }


}

class A {
    public $name = "a";

    /**
     * A constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function fn() :string {
        return $this->name;
    }

}