<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

use axy\errors\InvalidConfig;

/**
 * Building of environment wrapper instances
 */
class Factory
{
    /**
     * Returns a wrapper standard behaviour
     *
     * @return \axy\env\Env
     */
    public static function getStandard()
    {
        if (!self::$standard) {
            self::$standard = new Env();
        }
        return self::$standard;
    }

    /**
     * Creates an environment wrapper
     *
     * @param \axy\env\Env|\axy\env\Config|array $config
     * @return \axy\env\Env
     * @throws \axy\errors\InvalidConfig
     */
    public static function create($config = null)
    {
        if ($config === null) {
            return self::getStandard();
        }
        if (is_array($config) || ($config instanceof Config)) {
            return new Env($config);
        }
        if ($config instanceof Env) {
            return $config;
        }
        $message = 'Factory accepts Env, Config, array or NULL';
        throw new InvalidConfig('Env', $message, 0, null, __NAMESPACE__);
    }

    /**
     * @var \axy\env\Env
     */
    private static $standard;
}
