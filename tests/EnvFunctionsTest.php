<?php
/**
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\env\tests;

use axy\env\Env;
use axy\env\Stream;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * coversDefaultClass axy\env\Env
 */
class EnvFunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerInvalidConfig
     * @param mixed $aConfig
     * @return mixed
     * @expectedException \axy\errors\InvalidConfig
     */
    public function testInvalidConfig($aConfig)
    {
        return new Env($aConfig);
    }

    /**
     * @return array
     */
    public function providerInvalidConfig()
    {
        return [
            [
                1,
            ],
            [
                false,
            ],
            [
                [
                    'unknown' => true,
                ],
            ],
            [
                [
                    'functions' => false,
                ],
            ],
        ];
    }

    /**
     * covers ::isFunctionsExists
     */
    public function testIsFunctionsExists()
    {
        $config = [
            'functions' => [
                'time' => 'myTime',
                'my_function_that_not_exist_in_global' => 'myFunc',
                'strlen' => null,
            ],
        ];
        $env = new Env($config);
        $this->assertTrue($env->isFunctionExists('time'));
        $this->assertTrue($env->isFunctionExists('my_function_that_not_exist_in_global'));
        $this->assertTrue($env->isFunctionExists('strtotime'));
        $this->assertFalse($env->isFunctionExists('strlen'));
        $this->assertFalse($env->isFunctionExists('function_9231m123n_does__not_exist'));
    }

    /**
     * @dataProvider providerCall
     * @param string $name
     * @param array $arguments
     * @param mixed $result
     * @param string $exception [optional]
     */
    public function testCall($name, $arguments, $result, $exception = null)
    {
        $functions = [
            'strlen' => function ($string) {
                return strlen($string) * 3;
            },
            'my_defined_function' => function () {
                return array_sum(func_get_args());
            },
            'date' => null,
            'invalid' => true,
            'exc' => function ($x) {
                if ($x > 1) {
                    throw new \InvalidArgumentException($x.' > 1');
                }
            },
            'callbackNotExists' => 'callback__no_t_ex_ist',
        ];
        $config = [
            'functions' => $functions,
        ];
        $env = new Env($config);
        if (!$exception) {
            $this->assertSame($result, $env->__call($name, $arguments));
        } else {
            $this->setExpectedException($exception);
            $env->__call($name, $arguments);
        }
    }

    /**
     * @return array
     */
    public function providerCall()
    {
        return [
            'global' => [
                'str_replace',
                ['a', 'b', 'tra-la-la'],
                'trb-lb-lb',
            ],
            'override' => [
                'strlen',
                ['string'],
                18,
            ],
            'my' => [
                'my_defined_function',
                [1, 2, 3],
                6,
            ],
            'disable' => [
                'date',
                ['Y-m-d H:i:s'],
                null,
                'axy\errors\Disabled',
            ],
            'unknown' => [
                'this__is_U_nkn_own_functions',
                [1, 2, 3],
                null,
                'axy\errors\FieldNotExist',
            ],
            'invalid' => [
                'invalid',
                [1, 2, 3],
                null,
                'axy\errors\InvalidConfig',
            ],
            'exception' => [
                'exc',
                [5],
                null,
                'InvalidArgumentException',
            ],
            'callbackNotExists' => [
                'callbackNotExists',
                [],
                null,
                'axy\errors\FieldNotExist',
            ],
        ];
    }

    public function testCallMagic()
    {
        $headers = [];
        $config = [
            'functions' => [
                'header' => function ($h) use (&$headers) {
                    $headers[] = $h;
                }
            ],
        ];
        $env = new Env($config);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame(3, $env->strlen('123'));
        $env->header('Content-Type: text/html; charset=utf-8');
        $env->header('X-Header: 5');
        $expected = [
            'Content-Type: text/html; charset=utf-8',
            'X-Header: 5',
        ];
        $this->assertSame($expected, $headers);
    }

    public function testEcho()
    {
        $echoed = null;
        $config = [
            'functions' => [
                'echo' => function ($e) use (&$echoed) {
                    $echoed = $e;
                }
            ],
        ];
        $envNormal = new Env([]);
        $envEcho = new Env($config);
        ob_start();
        $envNormal->echo('one', 'two');
        $envEcho->echo('three', 'four');
        $out = ob_get_clean();
        $this->assertSame('one two', $out);
        $this->assertSame('three', $echoed);
        $this->assertFalse($envNormal->isFunctionExists('echo'));
        $this->assertTrue($envEcho->isFunctionExists('echo'));
    }

    public function testStreamField()
    {
        $env = new Env();
        $stream = $env->stdin;
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testStreamOut()
    {
        $env = new Env();
        $stream = $env->stdout;
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testStreamErr()
    {
        $env = new Env();
        $stream = $env->stderr;
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testStreamRead()
    {
        $string = uniqid();
        $resource = $this->getMemoryResourceWithString($string);

        $env = new Env([
            'stdin' => $resource
        ]);

        $readResult = $env->stdin->read(strlen($string));
        $this->assertSame($string, $readResult);
    }

    public function testReadLine()
    {
        $string = "one line\ntwo line";
        $resource = $this->getMemoryResourceWithString($string);

        $env = new Env([
            'stdin' => $resource
        ]);

        $this->assertSame("one line\n", $env->stdin->readLine());
        $this->assertSame("two line", $env->stdin->readLine());
        $this->assertSame(false, $env->stdin->readLine());
    }

    public function testWrite()
    {
        $resource = fopen('php://memory', 'rw');
        $env = new Env([
            'stdout' => $resource
        ]);

        $string = uniqid();
        $env->stdout->write($string);
        rewind($resource);
        $this->assertSame(fgets($resource), $string);

        $string = uniqid();
        $env->stdout->write($string, 3);
        rewind($resource);
        $this->assertSame(fread($resource, 3), substr($string, 0, 3));
    }

    public function testIsEOF()
    {
        $string = '';
        $resource = $this->getMemoryResourceWithString($string);

        $env = new Env([
            'stdin' => $resource
        ]);

        fgets($resource);
        $this->assertTrue($env->stdin->isEOF());
    }

    private function getMemoryResourceWithString($string)
    {
        $resource = fopen('php://memory', 'rw');
        fwrite($resource, $string);
        rewind($resource);
        return $resource;
    }
}
