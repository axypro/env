<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @author Constantin Conavaloff <constantin@conovaloff.com>
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
                if (!property_exists($this, $k)) {
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

    /**
     * Overriding the current time
     *
     * NULL - use time()
     * int or numeric string - a timestamp
     * string - a string for the `strtotime()` function
     *
     * 1234567890 - a timestamp (2009-02-14 02:31:30)
     * "1234567890" - similarly
     * "2015-11-04 10:11:12" - a time in the current timezone
     * "2015-11-04" - "2015-11-04 00:00:00"
     * "+1 month" - relative time for `strtotime()`
     *
     * @var string|int
     */
    public $time;

    /**
     * Is time changing?
     *
     * If specified the field `time` and `timeChanging` is `FALSE`
     * then `$eng->getCurrentTime()` is constantly throughout the script.
     *
     * If `timeChanging` then `time` specified the time of begin and `getCurrentTime()`
     * returns changing time (for long-term scenarios).
     *
     * @var bool
     */
    public $timeChanging = false;

    /**
     * Wrapper for $_SERVER
     *
     * @var array
     */
    public $server;

    /**
     * Wrapper for $_ENV
     *
     * @var array
     */
    public $env;

    /**
     * Wrapper for $_GET
     *
     * @var array
     */
    public $get;

    /**
     * Wrapper for $_POST
     *
     * @var array
     */
    public $post;

    /**
     * Wrapper for $_COOKIE
     *
     * @var array
     */
    public $cookie;

    /**
     * Wrapper for $_REQUEST
     *
     * @var array
     */
    public $request;

    /**
     * Wrapper for $_FILES
     *
     * @var array
     */
    public $files;

    /**
     * Standard I/O streams
     *
     * @var StreamContainer|array
     */
    public $streams;
}
