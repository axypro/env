<?php

namespace axy\env;

/**
 * when we read - we read from start of data and remove what we read
 * when we write - we concatenate to end
 */
class StreamTest implements IStream
{
    /**
     * @param string $data
     */
    public function __construct($data = '')
    {
        $this->writeData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = null)
    {
        if (is_null($length)) {
            $result = $this->data;
            $this->writeData(null);

            return $result;
        }

        $result = substr($this->data, 0, $length);
        $this->writeData(substr($this->data, $length));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
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
        $this->writeData(substr($this->data, $lineLength));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data, $length = null)
    {
        if (is_null($length)) {
            $write = substr($data, 0);
        } else {
            $write = substr($data, 0, $length);
        }

        $this->writeData($this->data . $write);

        return strlen($write);
    }

    /**
     * {@inheritdoc}
     */
    public function isEOF()
    {
        return $this->data === '';
    }

    /**
     * @param mixed $data
     */
    private function writeData($data)
    {
        if (!is_string($data)) {
            $data = '';
        }

        $this->data = $data;
    }

    /**
     * @var string
     */
    private $data;
}
