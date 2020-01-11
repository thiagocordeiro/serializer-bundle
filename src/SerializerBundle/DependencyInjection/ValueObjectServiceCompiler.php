<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Serializer\SerializerBundle\ValueObjectFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;

class ValueObjectServiceCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $classes = $container->getParameter('serializer.value_objects');

        $definitions = array_map(function (string $class): Definition {
            $definition = new Definition($class, [new Reference(Request::class), $class]);
            $definition->setFactory(new Reference(ValueObjectFactory::class));

            return $definition;
        }, $classes);

        $container->addDefinitions($definitions);
    }
}
