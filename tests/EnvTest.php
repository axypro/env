<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Env;

/**
 * coversDefaultClass axy\env\Env
 */
class EnvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerInvalidConfig
     * @param mixed $aConfig
     * @return mixed
     * @expectedException \axy\errors\InvalidConfig
     */
    public function testInvalidConfig($aConfig)
    {
        return new Env($aConfig);
    }

    /**
     * @return array
     */
    public function providerInvalidConfig()
    {
        return [
            [
                1,
            ],
            [
                false,
            ],
            [
                [
                    'unknown' => true,
                ],
            ],
            [
                [
                    'functions' => false,
                ],
            ],
        ];
    }

    /**
     * covers ::isFunctionsExists
     */
    public function testIsFunctionsExists()
    {
        $config = [
            'functions' => [
                'time' => 'myTime',
                'my_function_that_not_exists_in_global' => 'myFunc',
                'strlen' => null,
            ],
        ];
        $env = new Env($config);
        $this->assertTrue($env->isFunctionExists('time'));
        $this->assertTrue($env->isFunctionExists('my_function_that_not_exists_in_global'));
        $this->assertTrue($env->isFunctionExists('strtotime'));
        $this->assertFalse($env->isFunctionExists('strlen'));
        $this->assertFalse($env->isFunctionExists('function_9231m123n_does__not_exists'));
    }
}
