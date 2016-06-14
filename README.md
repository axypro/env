# axy\env

Abstracting access to the environment (PHP).

[![Latest Stable Version](https://img.shields.io/packagist/v/axy/env.svg?style=flat-square)](https://packagist.org/packages/axy/env)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/axypro/env/master.svg?style=flat-square)](https://travis-ci.org/axypro/env)
[![Coverage Status](https://coveralls.io/repos/axypro/env/badge.svg?branch=master&service=github)](https://coveralls.io/github/axypro/env?branch=master)
[![License](https://poser.pugx.org/axy/env/license)](LICENSE)

* The library does not require any dependencies (except composer packages).
* Tested on PHP 5.4+, PHP 7, HHVM (on Linux), PHP 5.5 (on Windows).
* Install: `composer require axy/env`.
* License: [MIT](LICENSE).

### Documentation

The library provides the abstract layer for access to the environment.
The environment is

 * The current time and timezone
 * PHP runtime settings
 * Input data (`$_GET`, `$_POST` ...)
 * Server data (`$_SERVER`, `$_ENV`)
 * HTTP headers and cookies
 * and etc... any parameter whose modification may affect other parts of the system

If some code has direct access to super-global arrays or calls functions like `time()` or `header()`
then it code becomes tied to the environment.
This code is difficult to test and configure.

The library provides the environment wrapper (an instance of `axy\env\Env` class).
The application code gets this wrapper from the outside and works with the environment via it.
By default the wrapper just delegates requests to standard functions.
But this behaviour can be redefined (for test or an other purpose).

#### Example

```php
use axy\env\Factory;

class Service
{
    public function __construct($env = null)
    {
        $this->env = Factory::create($env);
    }

    public function action()
    {
        $timeOfAction = $this->env->getCurrentTime();
        // ...
    }

    private $env;
}

// ...

$service = new Service();
$service->action();
```

By default will be used the standard environment and `$this->env->getCurrentTime()` returns the current time.
But this behaviour can be changed.

```php
$service = new Service(['time' => '2014-11-04 10:11:12']);
$service->action(); // the service will receive the specified time
```

#### Config

The environment wrapper with standard behaviour:

```php
use axy\env\Env;

$env = new Env();
```

The non-standard behavior is specified in the config:

```php
$config = [
    'time' => '2015-11-04 11:11:11',
    'get' => [
        'id' => 5,
    ],
];

$env = new Env($config);
```

The config specified via an array (as above) or via an instance of `axy\env\Config`:

```php
$config = new Config();
$config->time = '2015-11-04 11:11:11';
$config->get = ['id' => 5];

$env = new Env($config);
```

An array is simply, but the object supports autocomplete in IDE.

Specific parameters of the config are described in the relevant sections below.


##### Cloning

```php
$config = new Config();
$config->time = '2015-11-04 11:11:11';
$env = new Env($config);

$config->time = '2011-02-03 10:10:10';
echo date('j.m.Y', $env->getCurrentTime()); // 4.11.2015, config is cloned
```

#### Current Time

Check the current time (returns a timestamp):

```php
$env->getCurrentTime(void): int
```

Parameter `time` specified the current time.
It can be an int or a numeric string (timestamp) or a string for [strtotime](http://php.net/strtotime).

 * 1234567890 - a timestamp (2009-02-14 02:31:30)
 * "1234567890" - similarly
 * "2015-11-04 10:11:12" - a time in the current timezone
 * "2015-11-04" - "2015-11-04 00:00:00"
 * "+1 month" - relative time for `strtotime()`

```php

$config->time = '2015-11-04 00:01:02';
$env = new Env($config);

echo date('j.m.Y', $env->getCurrentTime()); // 4.11.2015
```

##### Changing time

`$env->getCurrentTime()` in the above example always returns the same time.
For long-lived scenarios, the time change can be important.

```php
/**
 * Cron daemon
 * Once an hour to kill, not to eat a lot of memory
 */

$startTime = time();

while (time() - $startTime() < 3500) {
    step();
    sleep(5);
}
```

If specified `timeChanging` then the time will be changing.

```php

$env = new Env([
    'time' => '1980-01-02 11:20:30',
    'timeChanging' => true,
]);

echo date('H:i:s', $env->getCurrentTime()).PHP_EOL; // 11:20:30
sleep(7);
echo date('H:i:s', $env->getCurrentTime()).PHP_EOL; // 11:20:37
```

##### Custom function instead `time()`

If `time` is not specified `getCurrentTime()` calls a wrapper of the global function `time()`
(see below section "Global Functions").
It can override.

```php
$config = [
    'functions' => [
        'time' => 'myOwnTimeImplementation',
    ],
];
```

#### Super-Globals

The super-globals arrays `$_SERVER`, `$_ENV`, `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_FILES` available via

 * `$env->server`
 * `$env->env`
 * `$env->get`
 * `$env->post`
 * `$env->request`
 * `$env->cookie`
 * `$env->files`

Their can override in the config:

```php
$config = [
    'get' => ['x' => 1],
];
$env = new Env($config);

$env->get['x']; // 1
$env->post['x']; // $_POST['x']
```

## Global Functions

Magic `__call` delegates to global functions.

```php
$env->strlen('string'); // 6
$env->header('Content-Type: text/plain'); // Send header
```

Overriding:

```php
$config = [
    'functions' => [
        'header' => function ($header) {
            // save header to a local storage
        },
    ],
];

$env = new Env();

$env->header('Content-Type: text/plain'); // The header will not be sent
```

Checking the existence of functions `$env->isFunctionExists(string $name):bool`.

`echo()` can also be overridden.

##### Example

Function `getallheaders()` not exist in all environments.

```php
if ($env->isFunctionExists('getallheaders')) {
    return $env->getallheaders();
}
```

Override:

```php
$config = [
    'functions' => [
        'getallheaders' => function () {
            if (function_exists('getallheaders')) {
                return getallheaders();
            } else {
                return parseServerVarsForHeaders();
            }
        },
    ],
];

// now $env->getallheaders() always available
```

Or

```php
$config = [
    'functions' => [
        'getallheaders' => null, // never
    ],
];
```

##### Functions list

It is intended for functions that access the environment.
In phpdoc for autocomplete lists the following:

 * [header](http://php.net/header)
 * [setcookie](http://php.net/setcookie)
 * [getallheaders](http://php.net/getallheaders)
 * [headers_list](http://php.net/headers_list)
 * [http_response_code](http://php.net/http_response_code)
 * [ini_set](http://php.net/ini_set)
 * [ini_get](http://php.net/ini_get)
 * [getenv](http://php.net/getenv)
 * [error_reporting](http://php.net/error_reporting)
 * [date_default_timezone_get](http://php.net/date_default_timezone_get)
 * [date_default_timezone_set](http://php.net/date_default_timezone_set)
 * [set_error_handler](http://php.net/set_error_handler)
 * [set_exception_handler](http://php.net/set_exception_handler)

But theoretically it can be used for any function.

#### Factory

```php
use axy\env\Factory;

$env = Factory::getStandard();
```

The method `getStandard()` returns an env wrapper for standard behaviour.

The method `create($config)` creates a wrapper with overriding behaviour.
It can taken:

an array

```php
$config = [
    'time' => 123456,
];

$env = Factory::create($config);
```

a `Config` instance

```php
$config = new Config();
$config->time = 123456,

$env = Factory::create($config);
```

or a `Env` instance itself

```php
$env1 = new Env([
    'time' => 123456,
]);

$env2 = Factory::create($env1); // $env1 === $env2
```

By default (`NULL`) specifies the standard wrapper.

The target service can take wrapper in different formats and not to deal with them.

```php
use axy\env\Factory;

class Service
{
    public function __construct($env = null)
    {
        $this->env = Factory::create($env);
    }

    // ...
}

$service = new Service(['time' => '2011-01-01']);
```

#### Exceptions

The library can throw the following exceptions:

* `axy\errors\InvalidConfig`
    * Unknown fields in a config (`['time' => 12345, 'unknown' => 67890]`)
    * Wrong type of fields (`['server' => 5]`, must be an array)
    * Wrong data in fields (`[time => 'wrong time string']`).
    * Function wrapper is not callable (detected during a call).
* `axy\errors\FieldNotFound`
    * Field not found (`echo $env->unknown;`).
    * Function not found (`echo $env->unknown(5);`).
* `axy\errors\Disabled`
    * Function disabled in the config
* `axy\errors\ContainerReadOnly`
    * `$env->server = []` or `unset($env->server)`.
