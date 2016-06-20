<?php

namespace axy\env;

interface IStream
{
    public function read($length);
    public function readLine();
    public function write($data, $length = null);
    public function isEOF();
}
