<?php
/**
 * Abstracting access to the environment
 *
 * @package axy\env
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/env/master/LICENSE MIT
 * @link https://github.com/axypro/env repository
 * @link https://packagist.org/packages/axy/env composer package
 * @uses PHP5.4+
 */

namespace axy\env;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
