<?php

namespace axy\env\tests;

use axy\env\Env;
use axy\env\StreamTest;

class StreamTestTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $string = uniqid();
        $streamTest = new StreamTest($string);

        $env = new Env([
            'streams' => [
                'stdin' => $streamTest
            ]
        ]);

        $result = $env->streams->stdin->read(strlen($string));
        $this->assertSame($string, $result);
    }

    public function testReadLine()
    {
        $string = "One line\nTwo line";
        $streamTest = new StreamTest($string);

        $env = new Env([
            'streams' => [
                'stdin' => $streamTest
            ]
        ]);


        $this->assertSame("One line\n", $env->streams->stdin->readLine());
        $this->assertSame('Two line', $env->streams->stdin->readLine());
        $this->assertSame(false, $env->streams->stdin->readLine());
    }

    public function testIsEOF()
    {
        $string = uniqid();
        $streamTest = new StreamTest($string);

        $env = new Env([
            'streams' => [
                'stdin' => $streamTest
            ]
        ]);

        $this->assertFalse($env->streams->stdin->isEOF());
        $env->streams->stdin->readLine();
        $this->assertTrue($env->streams->stdin->isEOF());
    }

    public function testWrite()
    {
        $string = uniqid();
        $streamTest = new StreamTest();

        $env = new Env([
            'streams' => [
                'test' => $streamTest
            ]
        ]);
        
        $this->assertSame(strlen($string), $env->streams->test->write($string));
        $this->assertSame($string, $env->streams->test->readLine());

        $this->assertSame(3, $env->streams->test->write($string, 3));
        $this->assertSame(substr($string, 0, 3), $env->streams->test->readLine());
    }
}
