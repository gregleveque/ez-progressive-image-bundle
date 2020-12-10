<?php


namespace Gie\EzProgressiveImageBundle\DependencyInjection\Compiler;

use Gie\EzProgressiveImageBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ImaginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('liip_imagine.filter.configuration')) {
            return;
        }

        $filterConfigDef = $container->findDefinition('liip_imagine.filter.configuration');
        $filterConfigDef->setClass(FilterConfiguration::class);

    }
}
