<?php

namespace Gie\EzProgressiveImageBundle;

use Gie\EzProgressiveImageBundle\DependencyInjection\Compiler\ImaginePass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzProgressiveImageBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ImaginePass(), PassConfig::TYPE_OPTIMIZE);
    }
}
