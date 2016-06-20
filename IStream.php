<?php

namespace axy\env;

interface IStream
{
    /**
     * @param int $length
     * @return string|bool
     */
    public function read($length = null);

    /**
     * @return string|bool
     */
    public function readLine();

    /**
     * @param string $data
     * @param int $length
     * @return int
     */
    public function write($data, $length = null);

    /**
     * @return bool
     */
    public function isEOF();
}
