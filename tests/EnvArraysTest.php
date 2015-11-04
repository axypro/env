<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Env;
use axy\errors\ContainerReadOnly;

/**
 * coversDefaultClass axy\env\Env
 */
class EnvArraysTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return mixed
     */
    public function testArrays()
    {
        $config = [
            'post' => ['x' => 1],
        ];
        $env = new Env($config);
        $this->assertTrue(isset($env->post));
        $this->assertTrue(isset($env->get));
        $this->assertFalse(isset($env->unknown));
        $this->assertSame(['x' => 1], $env->post);
        $this->assertSame($_SERVER, $env->server);
        $this->setExpectedException('axy\errors\FieldNotExist');
        return $env->unknown;
    }

    /**
     * @expectedException \axy\errors\ContainerReadOnly
     * @dataProvider providerArraysReadonly
     * @param callable $f
     */
    public function testArraysReadonly($f)
    {
        $env = new Env();
        $f($env);
    }

    /**
     * @return array
     */
    public function providerArraysReadonly()
    {
        return [
            [function ($e) {
                $e->post = 5;
            }],
            [function ($e) {
                $e->unknonw = 5;
            }],
            [function ($e) {
                unset($e->get);
            }],
            [function ($e) {
                unset($e->unkonwn);
            }],
        ];
    }
}
