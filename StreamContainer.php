<?php

namespace axy\env;

use axy\errors\FieldNotExist;
use axy\errors\NotValid;

/**
 * @property-read IStream $stdin
 * @property-read IStream $stdout
 * @property-read IStream $stderr
 */
class StreamContainer
{
    public function __construct(array $config = null)
    {
        if (is_null($config)) {
            $config = [];
        }

        foreach ($config as $streamName => $streamObj) {
            if (!($streamObj instanceof IStream)) {
                throw new NotValid($streamObj, $streamName . " don't implement IStream");
            }

            $this->streamList[$streamName] = $streamObj;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->streamList[$name])) {
            return $this->streamList[$name];
        }

        if (isset($this->streamDefault[$name])) {
            $this->streamList[$name] = new Stream(fopen('php://' . $name, $this->streamDefault[$name]));
            return $this->streamList[$name];
        }

        throw new FieldNotExist($name, $this, null, $this);
    }

    private $streamDefault = [
        'stdin' => 'w',
        'stdout' => 'r',
        'stderr' => 'w'
    ];

    private $streamList = [];
}
