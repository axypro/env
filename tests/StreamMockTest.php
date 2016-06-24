<?php

namespace axy\env\tests;

use axy\env\StreamMock;

/**
 * coverDefaultClass axy\env\StreamMock
 */
class StreamMockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::read
     * covers ::getContent
     * covers ::getPosition
     * covers ::setPosition
     * covers ::isEOF
     */
    public function testRead()
    {
        $mock = new StreamMock('This is content');
        $this->assertSame('This is content', $mock->getContent());
        $this->assertSame(0, $mock->getPosition());
        $this->assertFalse($mock->isEOF());
        $this->assertSame('This is ', $mock->read(8));
        $this->assertSame(8, $mock->getPosition());
        $this->assertFalse($mock->isEOF());
        $this->assertSame('content', $mock->read(100));
        $this->assertTrue($mock->isEOF());
        $this->assertSame(15, $mock->getPosition());
        $this->assertSame('', $mock->read(100));
        $this->assertSame(15, $mock->getPosition());
        $this->assertTrue($mock->isEOF());
        $mock->setPosition(3);
        $this->assertSame('s is content', $mock->read());
    }

    /**
     * covers ::readLine
     */
    public function testReadLine()
    {
        $mock = new StreamMock("\nThis\nis\ncontent");
        $this->assertSame("\n", $mock->readLine());
        $this->assertSame(1, $mock->getPosition());
        $mock->setPosition(4);
        $this->assertSame("s\n", $mock->readLine());
        $this->assertSame(6, $mock->getPosition());
        $this->assertSame('is', $mock->readLine(true));
        $this->assertSame(9, $mock->getPosition());
        $mock->read(3);
        $this->assertSame('tent', $mock->readLine());
        $this->assertSame(16, $mock->getPosition());
        $this->assertSame('', $mock->readLine());
        $this->assertSame(16, $mock->getPosition());
    }

    /**
     * covers ::write
     */
    public function testWrite()
    {
        $mock = new StreamMock('This is content');
        $mock->setPosition(5);
        $this->assertSame(2, $mock->write('no!', 2));
        $this->assertSame(7, $mock->getPosition());
        $this->assertSame('This no content', $mock->getContent());
        $this->assertSame(4, $mock->write(' AA '));
        $this->assertSame(11, $mock->getPosition());
        $this->assertSame('This no AA tent', $mock->getContent());
        $this->assertSame(11, $mock->write('New content'));
        $this->assertSame(22, $mock->getPosition());
        $this->assertSame('This no AA New content', $mock->getContent());
        $this->assertSame(3, $mock->write('!!!'));
        $this->assertSame(25, $mock->getPosition());
        $this->assertSame('This no AA New content!!!', $mock->getContent());
    }
}
