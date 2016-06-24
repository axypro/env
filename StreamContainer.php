<?php
/**
 * @package axy\env
 * @author Constantin Conavaloff <constantin@conovaloff.com>
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */


namespace axy\env;

use axy\errors\FieldNotExist;
use axy\errors\NotValid;

/**
 * Storage of streams
 *
 * @property-read IStream $stdin
 * @property-read IStream $stdout
 * @property-read IStream $stderr
 * @property-read IStream $input
 * @property-read IStream $output
 */
class StreamContainer
{
    /**
     * The constructor
     *
     * @param array $config [optional]
     * @throws \axy\errors\NotValid
     */
    public function __construct(array $config = null)
    {
        if ($config) {
            foreach ($config as $streamName => $streamObj) {
                if (!($streamObj instanceof IStream)) {
                    throw new NotValid($streamObj, $streamName." don't implement IStream");
                }
                $this->streams[$streamName] = $streamObj;
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (!isset($this->streamDefault[$name])) {
            if (!isset($this->streams[$name])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \axy\errors\FieldNotExist
     */
    public function __get($name)
    {
        if (!isset($this->streams[$name])) {
            if (isset($this->streamDefault[$name])) {
                $this->streams[$name] = new Stream(fopen('php://'.$name, $this->streamDefault[$name]));
            } else {
                throw new FieldNotExist($name, $this, null, $this);
            }
        }
        return $this->streams[$name];
    }

    /**
     * @var array (stream name => I/O mode)
     */
    private $streamDefault = [
        'stdin' => 'r',
        'stdout' => 'w',
        'stderr' => 'w',
        'input' => 'r',
        'output' => 'w',
    ];

    private $streams = [];
}
