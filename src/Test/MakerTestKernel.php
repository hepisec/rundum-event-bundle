<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rundum\EventBundle\Test;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Rundum\EventBundle\Maker\MakeEntityEvents;
use Rundum\EventBundle\RundumEventBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MakerBundle\DependencyInjection\CompilerPass\MakeCommandRegistrationPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class MakerTestKernel extends Kernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    private string $testRootDir;

    public function __construct(string $environment, bool $debug)
    {
        $this->testRootDir = sys_get_temp_dir().'/'.uniqid('rd_maker_', true);

        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new RundumEventBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
    }

    protected function configureRouting(RoutingConfigurator $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 123,
            'router' => [
                'utf8' => true,
            ],
            'http_method_override' => false,
        ]);

        $c->register('rundum_event_bundle.maker.make_entity_events', MakeEntityEvents::class)
            ->addTag('maker.command');
    }

    public function getProjectDir(): string
    {
        return $this->getRootDir();
    }

    public function getRootDir(): string
    {
        return $this->testRootDir;
    }

    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        // makes all makers public to help the tests
        foreach ($container->findTaggedServiceIds(MakeCommandRegistrationPass::MAKER_TAG) as $id => $tags) {
            $defn = $container->getDefinition($id);
            $defn->setPublic(true);
        }
    }
}
