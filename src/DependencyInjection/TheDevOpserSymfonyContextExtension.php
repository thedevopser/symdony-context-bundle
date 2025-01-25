<?php

namespace TheDevOpser\SymfonyContextBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use TheDevOpser\SymfonyContextBundle\Command\GenerateContextCommand;

class TheDevOpserSymfonyContextExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $container->register(GenerateContextCommand::class)
            ->addTag('console.command')
            ->setAutoconfigured(true)
            ->setAutowired(true);
    }

    public function getAlias(): string
    {
        return 'thedevopser_symfony_context';
    }
}