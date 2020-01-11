<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle;

use Serializer\SerializerBundle\DependencyInjection\ValueObjectServiceCompiler;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SerializerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValueObjectServiceCompiler(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
