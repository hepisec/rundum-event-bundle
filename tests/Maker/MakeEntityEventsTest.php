<?php

/* namespace Rundum\EventBundle\Tests\Maker;

use Rundum\EventBundle\Maker\MakeEntityEvents;
use Rundum\EventBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

class MakeEntityEventsTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeEntityEvents::class;
    }

    public function getTestDetails(): \Generator
    {
        yield 'it_makes_entity_events' => [$this->createMakerTest()
            ->addExtraDependencies('orm')
            ->run(function (MakerTestRunner $runner) {
                $runner->runMaker([
                    // bound-class
                    'Foo',
                ]);

                $this->runEntityEventsTest($runner, 'it_makes_entity_events');
            }),
        ];
    }

    private function runEntityEventsTest(MakerTestRunner $runner, string $filename): void
    {
        $runner->copy(
            'make-entity-events/tests',
            'tests'
        );

        $runner->runTests();
    }
} */