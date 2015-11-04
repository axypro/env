<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\helpers\Normalizer;
use axy\env\Config;

/**
 * coversDefaultClass axy\env\helpers\Normalizer
 */
class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::normalize
     */
    public function testNormalizeFunctions()
    {
        $config = new Config();
        Normalizer::normalize($config);
        $this->assertEquals([], $config->functions);
        $config->functions['time'] = 'time';
        Normalizer::normalize($config);
        $this->assertEquals(['time' => 'time'], $config->functions);
        $config->functions = null;
        Normalizer::normalize($config);
        $this->assertEquals([], $config->functions);
        $config->functions = false;
        $this->setExpectedException('axy\errors\InvalidConfig', 'field "functions" must be an array of callable');
        Normalizer::normalize($config);
    }

    /**
     * covers ::normalize()
     * @dataProvider providerNormalizeTime
     * @param int $time
     * @param string $expected
     */
    public function testNormalizeTime($time, $expected)
    {
        $config = new Config();
        $config->time = $time;
        if ($expected === false) {
            $this->setExpectedException('axy\errors\InvalidConfig');
            Normalizer::normalize($config);
        } elseif (is_array($expected)) {
            Normalizer::normalize($config);
            $this->assertInternalType('int', $config->time);
            $this->assertGreaterThanOrEqual($expected[0], $config->time);
            $this->assertLessThanOrEqual($expected[1], $config->time);
        } else {
            Normalizer::normalize($config);
            $this->assertSame($expected, $config->time);
        }
    }

    /**
     * @return array
     */
    public function providerNormalizeTime()
    {
        return [
            [
                1234567890,
                1234567890,
            ],
            [
                '1234567890',
                1234567890,
            ],
            [
                '2015-11-04 10:11:12',
                strtotime('2015-11-04 10:11:12'),
            ],
            [
                '2015-11-04',
                strtotime('2015-11-04 00:00:00'),
            ],
            [
                '+1 week',
                [time() + 7 * 86400, time() + 7 * 86400 + 2],
            ],
            [
                [],
                false,
            ],
            [
                true,
                false,
            ],
            [
                'qwe rty',
                false,
            ],
            [
                null,
                null,
            ],
        ];
    }
}
