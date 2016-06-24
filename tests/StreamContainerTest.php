<?php

namespace axy\env;

use axy\env\Env;
use axy\env\StreamMock;

class StreamContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testEnv()
    {
        $env = new Env();
        $this->assertInstanceOf('axy\env\StreamContainer', $env->streams);
        $this->assertInstanceOf('axy\env\Stream', $env->streams->stdin);
        $this->assertInstanceOf('axy\env\Stream', $env->streams->stdout);
        $this->assertInstanceOf('axy\env\Stream', $env->streams->stderr);
        $this->expectOutputString('test');
        $env->streams->output->write('test');
    }

    public function testDefault()
    {
        $container = new StreamContainer();
        $this->assertTrue(isset($container->stdin));
        $this->assertFalse(isset($container->none));
        $this->assertInstanceOf('axy\env\Stream', $container->stdin);
        $this->setExpectedException('axy\errors\FieldNotExist');
        $container->__get('none');
    }

    public function testChangeField()
    {
        $stream = new StreamMock();
        $container = new StreamContainer([
            'stdin' => $stream,
            'randomName' => $stream,
        ]);
        $this->assertTrue(isset($container->stdin));
        $this->assertTrue(isset($container->randomName));
        $this->assertFalse(isset($container->none));
        $this->assertInstanceOf('axy\env\StreamMock', $container->stdin);
        $this->assertInstanceOf('axy\env\StreamMock', $container->__get('randomName'));
        $this->setExpectedException('axy\errors\FieldNotExist');
        $container->__get('none');
    }

    public function testBadStream()
    {
        $stream = new \stdClass();
        $this->setExpectedException('axy\errors\NotValid');
        new StreamContainer([
            'stdin' => $stream,
        ]);
    }
}
