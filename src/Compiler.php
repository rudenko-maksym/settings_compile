<?php

namespace Drupal\Settings;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class Compiler
{
    public $globals = array(
        'databases',
        'cookie_domain',
        'conf',
        'installed_profile',
        'update_free_access',
        'db_url',
        'db_prefix',
        'drupal_hash_salt',
        'is_https',
        'base_secure_url',
        'base_insecure_url'
    );

    function __construct($configFile)
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new DrupalExtension);
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname($configFile))
        );
        $loader->load(basename($configFile));
        $container->compile();
        $this->config = $container->getParameter('drupal_settings');
    }

    function write($path)
    {
        $settings = "<?php\n";
        foreach ($this->config['settings'] as $settingName => $settingValue) {
          $setting = "\$$settingName=";
          $setting .= is_array($settingValue)
            ? $this->writeArray($settingValue)
            : $this->quote($settingValue);
          $settings .= "$setting;";
        }
        foreach ($this->config['ini'] as $iniDirective => $iniValue) {
            $settings .= "ini_set({$this->quote($iniDirective)}, {$this->quote($iniValue)});";
        }
        foreach ($this->config['include'] as $type => $includes) {
            foreach ($includes as $includePath) {
                $settings .= "$type {$this->quote($includePath)};";
            }
        }
        file_put_contents($path, $settings);
    }

    function writeArray($array)
    {
        $arrayString = 'array(';
        foreach ($array as $key => $value) {
            $arrayString .= $this->quote($key)
                . ' => '
                . $this->quote($value)
                . ',';
        }
        $arrayString .= ')';
        return $arrayString;
    }

    function quote($value)
    {
        if (is_array($value)) {
            return $this->writeArray($value);
        }
        if (!in_array($value[0], array('$', '%'))) {
            return '\'' . $value . '\'';
        }
        return str_replace('%', '', $value);
    }
}
