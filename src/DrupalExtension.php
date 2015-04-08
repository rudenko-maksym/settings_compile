<?php

namespace Drupal\Settings;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class DrupalExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Schema();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $config = $this->settingsPreprocess($config);
        $container->setParameter('drupal_settings', $config);
    }

    public function getAlias()
    {
        return 'drupal';
    }

    private function settingsPreprocess($config)
    {
        if (isset($config['settings']['db_url'])) {
            $db = &$config['settings']['databases']['default']['default'];
            $dbURL = parse_url($config['settings']['db_url']);
            $db['driver']   = $dbURL['scheme'];
            $db['username'] = $dbURL['user'];
            $db['password'] = $dbURL['pass'];
            $db['database'] = trim($dbURL['path'], '/');
            $db['host']     = $dbURL['host'];
        }

        return $config;
    }

}
