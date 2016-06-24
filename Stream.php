<?php
/**
 * @package axy\env
 * @author Constantin Conavaloff <constantin@conovaloff.com>
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

/**
 * Wrapper for stream resources
 */
class Stream implements IStream
{
    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = null)
    {
        if ($length !== null) {
            return fread($this->resource, $length);
        }
        $result = [];
        while (!feof($this->resource)) {
            $result[] = fread($this->resource, 512);
        }
        return implode('', $result);
    }

    /**
     * {@inheritdoc}
     */
    public function readLine($trim = false)
    {
        $result = fgets($this->resource);
        if ($result === false) {
            $result = '';
        } elseif ($trim) {
            $result = rtrim($result, "\n\r");
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data, $length = null)
    {
        if ($length !== null) {
            $result = fwrite($this->resource, $data, $length);
        } else {
            $result = fwrite($this->resource, $data);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isEOF()
    {
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($pos)
    {
        fseek($this->resource, $pos);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return ftell($this->resource);
    }

    /**
     * @var resource
     */
    private $resource;
}
