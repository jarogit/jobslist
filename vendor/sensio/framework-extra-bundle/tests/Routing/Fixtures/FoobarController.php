<?php

namespace Sensio\Bundle\FrameworkExtraBundle\Tests\Routing\Fixtures;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/base")
 * @Method("GET")
 */
class FoobarController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
    }

    /**
     * @Route("/new", name="new")
     * @Method("POST")
     */
    public function newAction()
    {
    }

    /**
     * @Route("/no-name")
     */
    public function noNameAction()
    {
    }
}
