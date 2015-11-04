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
}
