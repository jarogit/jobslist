<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneratedTwigComponentTest extends KernelTestCase
{
    public function testController()
    {
        $output = self::getContainer()->get('twig')->createTemplate("{{ component('{name}') }}")->render();

        $this->assertStringContainsString('<div data-controller="live" data-live-url-value=', $output);
        $this->assertStringContainsString('<!-- component html -->', $output);
    }
}
