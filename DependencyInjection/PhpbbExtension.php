<?php


namespace TeLiXj\PhpbbBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PhpbbExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('phpbb.database.entity_manager', $config['database']['entity_manager']);
        $container->setParameter('phpbb.database.prefix', $config['database']['prefix']);
        $container->setParameter('phpbb.session.cookie_name', $config['session']['cookie_name']);
        $container->setParameter('phpbb.session.login_page', $config['session']['login_page']);
        $container->setParameter('phpbb.session.force_login', $config['session']['force_login']);
        $container->setParameter('phpbb.roles', $config['roles']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
