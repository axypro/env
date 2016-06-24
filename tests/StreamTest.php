<?php

namespace axy\env\tests;

use axy\env\Stream;

/**
 * coversDefaultClass axy\env\Stream
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        if ($this->resource) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    /**
     * covers ::read
     */
    public function testStreamRead()
    {
        $stream = new Stream($this->getMemoryResourceWithString("test\n text"));
        $this->assertSame('te', $stream->read(2));
        $this->assertSame("st\n text", $stream->read());
        $this->assertSame('', $stream->read(3));
        $this->assertSame('', $stream->read());
    }

    /**
     * covers ::readLine
     */
    public function testReadLine()
    {
        $stream = new Stream($this->getMemoryResourceWithString("one line\ntwo line \nthree line"));
        $this->assertSame("one line\n", $stream->readLine());
        $this->assertSame('two line ', $stream->readLine(true));
        $this->assertSame('three line', $stream->readLine(false));
        $this->assertSame('', $stream->readLine());
    }

    /**
     * covers ::write
     */
    public function testWrite()
    {
        $resource = $this->getMemoryResourceWithString('one ', false);
        $stream = new Stream($resource);
        $this->assertSame(4, $stream->write('two '));
        $this->assertSame(2, $stream->write('three', 2));
        rewind($resource);
        $this->assertSame('one two th', fread($resource, 1000));
    }

    /**
     * covers ::isEOF
     */
    public function testIsEOF()
    {
        $stream = new Stream($this->getMemoryResourceWithString('one two'));
        $this->assertFalse($stream->isEOF());
        $stream->read(3);
        $this->assertFalse($stream->isEOF());
        /* bug in PHP 5.5.21+
        $stream->read(5);
        $this->assertTrue($stream->isEOF());
        $stream->read(15);
        $this->assertTrue($stream->isEOF());
        */
    }

    /**
     * covers ::setPosition
     * covers ::getPosition
     */
    public function testPosition()
    {
        $stream = new Stream($this->getMemoryResourceWithString('test'));
        $this->assertSame(0, $stream->getPosition());
        $stream->read(2);
        $this->assertSame(2, $stream->getPosition());
        $stream->setPosition(3);
        $this->assertSame(3, $stream->getPosition());
        $this->assertSame('t', $stream->read(3));
        $this->assertSame(4, $stream->getPosition());
        $stream->setPosition(1);
        $stream->write('!');
        $this->assertSame(2, $stream->getPosition());
        rewind($this->resource);
        $this->assertSame('t!st', fread($this->resource, 10));
    }

    /**
     * @param string $string
     * @param bool $rewind [optional]
     * @return resource
     */
    private function getMemoryResourceWithString($string, $rewind = true)
    {
        $resource = fopen('php://memory', 'r+w');
        fwrite($resource, $string);
        $this->resource = $resource;
        if ($rewind) {
            rewind($resource);
        }
        return $resource;
    }

    /**
     * @var resource
     */
    private $resource;
}
