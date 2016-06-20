<?php

namespace axy\env;

class Stream implements IStream
{
    /**
     * @var resource
     */
    private $resource;

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
        if (!is_null($length)) {
            return fread($this->resource, $length);
        }

        $result = '';
        while (!feof($this->resource)) {
            $result .= fread($this->resource, $this->dataSegment);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function readLine()
    {
        return fgets($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function write($data, $length = null)
    {
        if (is_null($length)) {
            return fwrite($this->resource, $data);
        }

        return fwrite($this->resource, $data, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function isEOF()
    {
        return feof($this->resource);
    }

    private $dataSegment = 10;
}
