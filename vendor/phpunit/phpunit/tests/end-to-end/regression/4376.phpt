--TEST--
https://github.com/sebastianbergmann/phpunit/issues/4376
--FILE--
<?php declare(strict_types=1);
$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/4376/';

require_once __DIR__ . '/../../bootstrap.php';
PHPUnit\TextUI\Command::main();
--EXPECTF--
PHPUnit %s #StandWithUkraine

E                                                                   1 / 1 (100%)

Time: %s, Memory: %s

There was 1 error:

1) Test::testOne
Error: Class %sC%s not found

%sTest.php:%d

ERRORS!
Tests: 1, Assertions: 0, Errors: 1.
