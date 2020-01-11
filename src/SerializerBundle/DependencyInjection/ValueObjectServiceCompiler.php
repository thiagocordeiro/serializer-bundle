<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueObjectServiceCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $classes = $container->getParameter('serializer.value_objects');

        array_map(function (string $id) use ($container) : void {
            $definition = new Definition($id);
            $definition->addArgument($id);
            $definition->setFactory(new Reference(HttpValueObjectFactory::class));

            $container->setDefinition($id, $definition);
        }, $classes);
    }
}
