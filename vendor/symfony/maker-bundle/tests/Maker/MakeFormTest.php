<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\Tests\Maker;

use Symfony\Bundle\MakerBundle\Maker\MakeForm;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

class MakeFormTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeForm::class;
    }

    public function getTestDetails()
    {
        yield 'it_generates_basic_form' => [$this->createMakerTest()
            ->run(function (MakerTestRunner $runner) {
                $runner->runMaker([
                    // form name
                    'FooBar',
                    '',
                ]);

                $this->runFormTest($runner, 'it_generates_basic_form.php');
            }),
        ];

        yield 'it_generates_form_with_entity' => [$this->createMakerTest()
            ->addExtraDependencies('orm')
            ->run(function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-form/Property.php',
                    'src/Entity/Property.php'
                );
                $runner->copy(
                    'make-form/SourFood.php',
                    'src/Entity/SourFood.php'
                );

                $runner->runMaker([
                    // Entity name
                    'SourFoodType',
                    'SourFood',
                ]);

                $this->runFormTest($runner, 'it_generates_form_with_entity.php');
            }),
        ];

        yield 'it_generates_form_with_non_entity_dto' => [$this->createMakerTest()
            ->run(function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-form/TaskData.php',
                    'src/Form/Data/TaskData.php'
                );

                $runner->runMaker([
                    // Entity name
                    'TaskType',
                    '\\App\\Form\\Data\\TaskData',
                ]);

                $this->runFormTest($runner, 'it_generates_form_with_non_entity_dto.php');
            }),
        ];

        yield 'it_generates_form_with_single_table_inheritance_entity' => [$this->createMakerTest()
            ->addExtraDependencies('orm')
            ->run(function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-form/inheritance/Food.php',
                    'src/Entity/Food.php'
                );
                $runner->copy(
                    'make-form/inheritance/SourFood.php',
                    'src/Entity/SourFood.php'
                );

                $runner->runMaker([
                    // Entity name
                    'SourFoodType',
                    'SourFood',
                ]);

                $this->runFormTest($runner, 'it_generates_form_with_single_table_inheritance_entity.php');
            }),
        ];

        yield 'it_generates_form_with_embeddable_entity' => [$this->createMakerTest()
            ->addExtraDependencies('orm')
            ->run(function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-form/embeddable/Food.php',
                    'src/Entity/Food.php'
                );
                $runner->copy(
                    'make-form/embeddable/Receipt.php',
                    'src/Entity/Receipt.php'
                );

                $runner->runMaker([
                    // Entity name
                    'FoodType',
                    'Food',
                ]);

                $this->runFormTest($runner, 'it_generates_form_with_embeddable_entity.php');
            }),
        ];
    }

    private function runFormTest(MakerTestRunner $runner, string $filename)
    {
        $runner->copy(
            'make-form/tests/'.$filename,
            'tests/GeneratedFormTest.php'
        );

        $runner->configureDatabase();
        $runner->runTests();
    }
}
