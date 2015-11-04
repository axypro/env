<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Config;

/**
 * coversDefaultClass axy\env\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $configS = new Config();
        $aConfig = [
            'functions' => [
                'time' => 'time',
            ],
        ];
        $configM = new Config($aConfig);
        $this->assertNotEquals($configS, $configM);
        $configS->functions['time'] = 'time';
        $this->assertEquals($configS, $configM);
        $aConfigInvalid = [
            'functions' => [
                'time' => 'time',
            ],
            'unknown' => true,
        ];
        $this->setExpectedException('axy\errors\InvalidConfig', '"unknown" is unknown');
        return new Config($aConfigInvalid);
    }
}
