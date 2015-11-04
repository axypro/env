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
}
