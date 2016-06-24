<?php
/**
 * @package axy\env
 * @author Constantin Conavaloff <constantin@conovaloff.com>
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env;

/**
 * Mock stream for tests
 */
class StreamMock implements IStream
{
    /**
     * The constructor
     *
     * @param string $content [optional]
     * @param int $position [optional]
     */
    public function __construct($content = '', $position = 0)
    {
        $this->content = $content;
        $this->position = $position ?: 0;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = null)
    {
        $len = strlen($this->content);
        if ($length === null) {
            $length = $len - $this->position;
        } else {
            $pos = $this->position + $length;
            if ($pos >= $len) {
                $length += $len - $pos;
            }
        }
        if ($length > 0) {
            $result = substr($this->content, $this->position, $length);
        } else {
            $result = '';
        }
        $this->position += $length;
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function readLine($trim = false)
    {
        $pos = strpos($this->content, "\n", $this->position);
        if ($pos === false) {
            $result = substr($this->content, $this->position);
            if ($result === false) {
                $result = '';
            }
            $this->position = strlen($this->content);
        } else {
            $result = substr($this->content, $this->position, $pos - $this->position + 1);
            if ($result === false) {
                $result = '';
            } elseif ($trim) {
                $result = rtrim($result, "\r\n");
            }
            $this->position = $pos + 1;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data, $length = null)
    {
        if ($length !== null) {
            $data = substr($data, 0, $length);
        }
        $lenD = strlen($data);
        if ($data === '') {
            return $lenD;
        }
        $begin = substr($this->content, 0, $this->position);
        $pos = $this->position + $lenD;
        $len = strlen($this->content);
        if ($this->position === $len) {
            $this->content = $begin.$data;
            $this->position = $pos;
            return $lenD;
        }
        if ($pos < $len) {
            $end = substr($this->content, $pos);
        } else {
            $end = '';
        }
        $this->content = $begin.$data.$end;
        $this->position = $pos;
        return $lenD;
    }

    /**
     * {@inheritdoc}
     */
    public function isEOF()
    {
        return ($this->position >= strlen($this->content));
    }

    /**
     * Sets the internal content
     *
     * @param string $content
     * @param int $position [optional]
     */
    public function setContent($content, $position = 0)
    {
        $this->content = $content;
        $this->position = $position ?: 0;
    }

    /**
     * Sets the pointer
     *
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the internal content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the pointer
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $position;
}
