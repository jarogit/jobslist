<?php

namespace Sensio\Bundle\FrameworkExtraBundle\Tests\EventListener\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class FooControllerEntityAttributeAtMethod
{
    #[Entity('foo', expr: 'repository.find(id)')]
    public function barAction($foo)
    {
    }
}
