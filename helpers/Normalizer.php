<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\helpers;

use axy\env\Config;
use axy\env\Stream;
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
        self::normalizeArrays($config);
        self::normalizeResource($config);
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

    /**
     * @param \axy\env\Config $config
     * @throws \axy\errors\InvalidConfig
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private static function normalizeArrays(Config $config)
    {
        $conf = [
            'server' => $_SERVER,
            'env' => $_ENV,
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            'request' => $_REQUEST,
            'files' => $_FILES,
        ];
        foreach ($conf as $k => $v) {
            if ($config->$k === null) {
                $config->$k = $v;
            } elseif (!is_array($config->$k)) {
                $message = 'field "'.$k.'" must be an array';
                throw new InvalidConfig('Env', $message, 0, null, __NAMESPACE__);
            }
        }
    }

    private static function normalizeResource(Config $config)
    {
        $resourceDefault = [
            'stdin' => fopen("php://stdin", "r"),
            'stdout' => fopen("php://stdout", "w"),
            'stderr' => fopen("php://stderr", "r")
        ];

        foreach ($resourceDefault as $resourceName => $resource) {
            if (isset($config->$resourceName)) {
                $config->$resourceName = new Stream($config->$resourceName);
            } else {
                $config->$resourceName = new Stream($resource);
            }
        }
    }
}
