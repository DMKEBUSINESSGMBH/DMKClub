<?php

namespace DMKClub\Bundle\MemberBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DMKClubMemberExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('autowire.yml');
        $loader->load('controllers.yml');
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('importexport.yml');
        $loader->load('mass_action.yml');

        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }
}
