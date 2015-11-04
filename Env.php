<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

use axy\env\helpers\Normalizer;
use axy\errors\InvalidConfig;

/**
 * Wrapper for the environment
 */
class Env
{
    /**
     * The constructor
     *
     * @param \axy\env\Config|array $config [optional]
     * @throws \axy\errors\InvalidConfig
     */
    public function __construct($config = null)
    {
        if ($config === null) {
            $config = new Config();
        } else {
            if (is_array($config)) {
                $config = new Config($config);
            } elseif ($config instanceof Config) {
            } else {
                throw new InvalidConfig('Env', 'config must be an array or an instance of \axy\env\Config');
            }
            Normalizer::normalize($config);
        }
        $this->config = $config;
    }

    /**
     * @var \axy\env\Config
     */
    private $config;
}
