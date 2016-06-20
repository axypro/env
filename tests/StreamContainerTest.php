<?php
/**
 * Created by PhpStorm.
 * User: conovaloff
 * Date: 20.06.16
 * Time: 16:42
 */

namespace axy\env;

class StreamContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $streamContainer = new StreamContainer();

        $this->assertInstanceOf(Stream::class, $streamContainer->stdin);

        $this->setExpectedException('axy\errors\FieldNotExist');
        $streamContainer->notExist;
    }

    public function testChangeField()
    {
        $stream = new StreamTest();
        $streamContainer = new StreamContainer([
            'stdin' => $stream,
            'randomName' => $stream
        ]);
        $this->assertInstanceOf(StreamTest::class, $streamContainer->stdin);
        $this->assertInstanceOf(StreamTest::class, $streamContainer->randomName);

        $this->setExpectedException('axy\errors\FieldNotExist');
        $this->assertInstanceOf(StreamTest::class, $streamContainer->notExist);
    }
}
