<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @author Constantin Conavaloff <constantin@conovaloff.com>
 */

namespace axy\env;

/**
 * Interface of standard streams (stdin, stdout, stderr)
 */
interface IStream
{
    /**
     * Reads data from the stream
     *
     * @param int $length [optional]
     *        number of bytes read (NULL - reads the full stream)
     * @return string
     *         the read string (empty stream - empty string)
     */
    public function read($length = null);

    /**
     * Reads a line from the stream
     *
     * @param bool $trim [optional]
     *        trims new line symbols
     * @return string
     */
    public function readLine($trim = false);

    /**
     * Writes data to the stream
     *
     * @param string $data
     * @param int $length [optional]
     * @return int
     *         the number of bytes written,
     */
    public function write($data, $length = null);

    /**
     * Checks for end of stream
     *
     * @return bool
     */
    public function isEOF();

    /**
     * Seeks on the stream pointer
     *
     * @param int $pos
     */
    public function setPosition($pos);

    /**
     * Returns the current position of the stream pointer
     *
     * @return int
     */
    public function getPosition();
}
