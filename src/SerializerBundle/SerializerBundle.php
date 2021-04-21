<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle;

use Serializer\SerializerBundle\DependencyInjection\ValueObjectServiceCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SerializerBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ValueObjectServiceCompiler());
    }
}
