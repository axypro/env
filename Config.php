<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

use axy\errors\InvalidConfig;

/**
 * The config of an environment object
 */
class Config
{
    /**
     * The constructor
     *
     * @param array $config [optional]
     *        parameters for merging
     * @throws \axy\errors\InvalidConfig
     *         some parameters are unknown
     */
    public function __construct(array $config = null)
    {
        if ($config !== null) {
            foreach ($config as $k => $v) {
                if (!isset($this->$k)) {
                    $message = 'parameter "'.$k.'" is unknown';
                    throw new InvalidConfig('Env', $message, 0, null, $this);
                }
                $this->$k = $v;
            }
        }
    }

    /**
     * Overriding global functions
     *
     * the function name => the callback
     *
     * @var callable[]
     */
    public $functions = [];
}
