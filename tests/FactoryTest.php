<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Factory;
use axy\env\Env;
use axy\env\Config;

/**
 * coversDefaultClass axy\env\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::getStandard
     */
    public function testGetStandard()
    {
        $env = Factory::getStandard();
        $this->assertInstanceOf('axy\env\Env', $env);
        $this->assertEquals(time(), $env->getCurrentTime(), '', 2);
        $this->assertSame($env, Factory::getStandard());
    }

    /**
     * covers ::create
     */
    public function testCreateNull()
    {
        $this->assertSame(Factory::getStandard(), Factory::create());
    }

    /**
     * covers ::create
     */
    public function testCreateEnv()
    {
        $env = new Env(['time' => 10]);
        $this->assertSame($env, Factory::create($env));
    }

    /**
     * covers ::create
     */
    public function testCreateConfig()
    {
        $config = new Config();
        $config->time = 12345;
        $env = Factory::create($config);
        $this->assertInstanceOf('axy\env\Env', $env);
        $this->assertSame(12345, $env->getCurrentTime());
    }

    /**
     * covers ::create
     */
    public function testCreateArray()
    {
        $config = [
            'time' => 1234,
        ];
        $env = Factory::create($config);
        $this->assertInstanceOf('axy\env\Env', $env);
        $this->assertSame(1234, $env->getCurrentTime());
    }

    /**
     * covers ::create
     * @dataProvider providerInvalidConfig
     * @param mixed $config
     * @expectedException \axy\errors\InvalidConfig
     */
    public function testInvalidConfig($config)
    {
        Factory::create($config);
    }

    /**
     * @return array
     */
    public function providerInvalidConfig()
    {
        return [
            [true],
            [5],
            [false],
            [$this],
            [
                [
                    'unknown' => 5,
                ],
            ],
            [
                [
                    'time' => [],
                ],
            ],
            [
                [
                    'get' => 37,
                ],
            ],
        ];
    }
}
