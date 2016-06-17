<?php

namespace axy\env;

class Stream
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

    public function readLine($prompt = null)
    {
        if (stream_get_meta_data($this->resource)['uri'] == 'php://stdin') {
            return readline($prompt);
        }

        return rtrim($this->readOneLineFromResource(), PHP_EOL);
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

    private function readOneLineFromResource()
    {
        return fgets($this->resource);
    }
}
