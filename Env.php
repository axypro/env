<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

use axy\env\helpers\Normalizer;
use axy\errors\InvalidConfig;
use axy\errors\FieldNotExist;
use axy\errors\Disabled;

/**
 * Wrapper for the environment
 *
 * @method void header(string $string, bool $replace = true, int $http_response_code = null)
 * @method bool setcookie(string $name,string $val,int $e=0,string $p=null,string $d=null,bool $s=false,bool $h = false)
 * @method array getallheaders()
 * @method array headers_list()
 * @method int http_response_code(int $response_code = null)
 * @method string ini_set(string $varname, string $newvalue)
 * @method string ini_get(string $varname)
 * @method string getenv(string $varname)
 * @method int error_reporting(int $level = null)
 * @method string date_default_timezone_get()
 * @method bool date_default_timezone_set(string $timezone_identifier)
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
        $this->initTime();
    }

    /**
     * Checks if a function exists (global or overloaded)
     *
     * @param string $name
     * @return bool
     */
    public function isFunctionExists($name)
    {
        $functions = $this->config->functions;
        if (array_key_exists($name, $functions)) {
            return ($functions[$name] !== null);
        }
        return function_exists($name);
    }

    /**
     * Calls a function
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \axy\errors\InvalidConfig
     *         the function in the config is not callable
     * @throws \axy\errors\Disabled
     *         the function is disabled in the config
     * @throws \axy\errors\FieldNotExist
     *         the function is not found
     * @throws \Exception
     *         an exception inside the function
     */
    public function __call($name, $arguments)
    {
        $functions = $this->config->functions;
        if (array_key_exists($name, $functions)) {
            if ($functions[$name] === null) {
                throw new Disabled($name, null, $this);
            }
            $callback = $functions[$name];
            if (!is_callable($callback)) {
                if (!is_callable($callback, true)) {
                    $message = 'Function "'.$name.'" is not callable';
                    throw new InvalidConfig('Env', $message, 0, null, $this);
                }
                throw new FieldNotExist($name, 'global', null, $this);
            }
            return call_user_func_array($callback, $arguments);
        }
        if (!function_exists($name)) {
            throw new FieldNotExist($name, 'global', null, $this);
        }
        return call_user_func_array($name, $arguments);
    }

    /**
     * Returns a timestamp of the current time
     *
     * @return int
     */
    public function getCurrentTime()
    {
        $config = $this->config;
        $time = $config->time;
        if ($time !== null) {
            if ($config->timeChanging) {
                return $this->__call('time', []) + $time;
            }
            return $time;
        }
        return $this->__call('time', []);
    }

    private function initTime()
    {
        if (($this->config->time !== null) && ($this->config->timeChanging)) {
            $this->config->time -= $this->__call('time', []);
        }
    }

    /**
     * @var \axy\env\Config
     */
    private $config;
}
