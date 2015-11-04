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
}
