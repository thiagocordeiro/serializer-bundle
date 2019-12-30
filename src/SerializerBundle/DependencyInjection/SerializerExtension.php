<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SerializerExtension extends Extension
{
    private const CONFIG_DIR = __DIR__ . '/../Resources/config';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator(self::CONFIG_DIR);
        $loader = new YamlFileLoader($container, $fileLocator);

        $loader->load('serializer.yaml');
    }
}
