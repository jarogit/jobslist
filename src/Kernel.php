<?php

namespace App;

use GuzzleHttp\HandlerStack;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public static function getGuzzleHandlerStack(): HandlerStack
    {
        static $stack;

        return $stack ?: $stack = HandlerStack::create();
    }
}
