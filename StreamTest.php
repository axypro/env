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
        if ($this->isEOF()) {
            return false;
        }

        $lineList = preg_split("/(\\r\\n|\\r|\\n)/", $this->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = $lineList[0];

        if (isset($lineList[1])) {
            $result .= $lineList[1];
        }

        $lineLength = strlen($result);
        $this->data = substr($this->data, $lineLength);

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
        return $this->data === '' || $this->data === false;
    }
}
