<?php

namespace axy\env;

class Stream implements IStream
{
    /**
     * @var resource
     */
    private $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function read($length)
    {
        return fread($this->resource, $length);
    }

    public function readLine()
    {
        return fgets($this->resource);
    }

    public function write($data, $length = null)
    {
        if (is_null($length)) {
            return fwrite($this->resource, $data);
        }

        return fwrite($this->resource, $data, $length);
    }

    public function isEOF()
    {
        return feof($this->resource);
    }
}
