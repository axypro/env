<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\helpers;

use axy\env\Config;
use axy\errors\InvalidConfig;

/**
 * Normalizer of config
 */
class Normalizer
{
    /**
     * Normalizes a config
     *
     * @param \axy\env\Config $config
     * @throws \axy\errors\InvalidConfig
     */
    public static function normalize(Config $config)
    {
        self::normalizeFunctions($config);
        self::normalizeTime($config);
    }

    /**
     * @param \axy\env\Config $config
     * @throws \axy\errors\InvalidConfig
     */
    private static function normalizeFunctions(Config $config)
    {
        if ($config->functions === null) {
            $config->functions = [];
        } elseif (!is_array($config->functions)) {
            $message = 'field "functions" must be an array of callable';
            throw new InvalidConfig('Env', $message, 0, null, __NAMESPACE__);
        }
    }

    /**
     * @param \axy\env\Config $config
     * @throws \axy\errors\InvalidConfig
     */
    private static function normalizeTime(Config $config)
    {
        $time = $config->time;
        if (($time === null) || (is_int($time))) {
            return;
        }
        if (!is_string($time)) {
            $message = 'field "time" must be a timestamp or a string for strtotime()';
            throw new InvalidConfig('Env', $message, 0, null, __NAMESPACE__);
        }
        $ts = (int)$time;
        if ((string)$ts !== $time) {
            $ts = strtotime($time);
            if ($ts === false) {
                $message = 'field "time" has invalid format';
                throw new InvalidConfig('Env', $message, 0, null, __NAMESPACE__);
            }
        }
        $config->time = $ts;
    }
}
