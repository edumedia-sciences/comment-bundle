<?php

namespace eduMedia\CommentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class eduMediaCommentExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');

        if (class_exists('Symfony\Component\Console\Command\Command')) {
            $loader->load('console-command.yaml');
        }
    }

    public function getAlias(): string
    {
        return 'edumedia_comment';
    }
}
