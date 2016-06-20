<?php

namespace axy\env;

class StreamTest implements IStream
{
    /**
     * @var string
     */
    private $data;

    /**
     * @param string $data
     */
    public function __construct($data = '')
    {
        $this->data = $data;
    }

    /**
     * То что читаем из строки - удаляем
     *
     * @param int $length
     *
     * @return mixed
     */
    public function read($length)
    {
        $result = substr($this->data, 0, $length);
        $this->data = substr($this->data, $length);

        return $result;
    }

    public function readLine()
    {
        $result = '';

        if ($this->isEOF()) {
            return false;
        }

        do {
            $char = $this->read(1);

            if ($char === false) {
                break;
            }

            $result .= $char;
        } while ($char !== PHP_EOL);

        return $result;
    }

    public function write($data, $length = null)
    {
        if (is_null($length)) {
            $write = substr($data, 0);
        } else {
            $write = substr($data, 0, $length);
        }

        $this->data .= $write;

        return strlen($write);
    }

    public function isEOF()
    {
        return empty($this->data);
    }
}
