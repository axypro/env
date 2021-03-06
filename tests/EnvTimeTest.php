<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Env;
use axy\env\Config;

/**
 * coversDefaultClass axy\env\Env
 */
class EnvTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::getCurrentTime()
     */
    public function testNativeTime()
    {
        $env = new Env();
        $now = time();
        $actual = $env->getCurrentTime();
        $this->assertInternalType('int', $actual);
        $this->assertEquals($now, $actual, '', 30);
    }

    /**
     * covers ::getCurrentTime()
     */
    public function testCustomTime()
    {
        $config = [
            'time' => 1234567890,
        ];
        $env = new Env($config);
        $this->assertSame(1234567890, $env->getCurrentTime());
    }

    /**
     * covers ::getCurrentTime()
     */
    public function testNotChangingTime()
    {
        $time = strtotime('2015-11-04 12:00:00');
        $config = [
            'time' => '2015-11-03 12:00:00',
            'timeChanging' => false,
            'functions' => [
                'time' => function () use (&$time) {
                    return $time;
                },
            ],
        ];
        $env = new Env($config);
        $this->assertSame('2015-11-03 12:00:00', date('Y-m-d H:i:s', $env->getCurrentTime()));
        $time += 10;
        $this->assertSame('2015-11-03 12:00:00', date('Y-m-d H:i:s', $env->getCurrentTime()));
        /** @noinspection PhpUnusedLocalVariableInspection */
        $time += 3600;
        $this->assertSame('2015-11-03 12:00:00', date('Y-m-d H:i:s', $env->getCurrentTime()));
    }

    /**
     * covers ::getCurrentTime()
     * @noinspection PhpUnusedLocalVariableInspection
     */
    public function testChangingTime()
    {
        $time = strtotime('2015-11-04 12:00:00');
        $config = [
            'time' => '2015-11-03 12:00:00',
            'timeChanging' => true,
            'functions' => [
                'time' => function () use (&$time) {
                    return $time;
                },
            ],
        ];
        $env = new Env($config);
        $this->assertSame('2015-11-03 12:00:00', date('Y-m-d H:i:s', $env->getCurrentTime()));
        $time += 10;
        $this->assertSame('2015-11-03 12:00:10', date('Y-m-d H:i:s', $env->getCurrentTime()));
        /** @noinspection PhpUnusedLocalVariableInspection */
        $time += 3600;
        $this->assertSame('2015-11-03 13:00:10', date('Y-m-d H:i:s', $env->getCurrentTime()));
    }

    public function testClone()
    {
        $config = new Config();
        $config->time = 1234567890;
        $env = new Env($config);
        $this->assertSame(1234567890, $env->getCurrentTime());
        $config->time = 5454345;
        $this->assertSame(1234567890, $env->getCurrentTime());
    }
}
