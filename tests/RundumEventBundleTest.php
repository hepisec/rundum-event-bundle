<?php

namespace Rundum\EventBundle\Tests;

use Nyholm\BundleTest\TestKernel;
use Rundum\EventBundle\Maker\MakeEntityEvents;
use Rundum\EventBundle\RundumEventBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\HttpKernel\KernelInterface;

class RundumEventBundleTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(MakerBundle::class);
        $kernel->addTestBundle(RundumEventBundle::class);
        $kernel->addTestConfig(__DIR__ . '/../config/services.yaml');
        $kernel->handleOptions($options);

        return $kernel;
    }    

    public function testInitBundle(): void
    {
        $container = self::getContainer();        

        // Test if your services exists
        $this->assertTrue($container->has(MakeEntityEvents::class));
        $service = $container->get(MakeEntityEvents::class);
        $this->assertInstanceOf(MakeEntityEvents::class, $service);
    }
}