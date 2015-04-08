<?php

namespace Tests\Drupal\Settings;

use Drupal\Settings\Compiler;

class CompilerTest extends BaseTestCase
{
    public function testThatCompilesSettings()
    {
        $configPath = $this->createConfigFile('config.yml',
<<<EOF
drupal:
  settings:
    databases:
      default:
        default:
          database: drupal_db
          username: drupal_user
          password: drupal_pass
          driver: mysql
    conf:
      site_name: My site
      some_var: bar
EOF
        );

        $compiler = new Compiler($configPath);
        $compiler->write($this->getSettingsTargetPath());

        $this->assertSettingsVarEquals('databases', array(
            'default' => array(
                'default' => array(
                    'database' => 'drupal_db',
                    'username' => 'drupal_user',
                    'password' => 'drupal_pass',
                    'driver' => 'mysql',
                )
            )
        ));

        $this->assertSettingsVarEquals('conf', array(
            'site_name' => 'My site',
            'some_var' => 'bar'
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testThatCompilesIni()
    {
        $configPath = $this->createConfigFile('config.yml',
<<<EOF
drupal:
  settings:
    databases:
      default:
        default:
          database: drupal_db
          username: drupal_user
          password: drupal_pass
          driver: mysql
  ini:
   date.timezone: Europe/Helsinki
   display_errors: On
EOF
        );

        $this->compileConfig($configPath);

        $this->assertIniSettingEquals('date.timezone', 'Europe/Helsinki');
        $this->assertIniSettingEquals('display_errors', 'On');
    }

    public function testPhpConstantsUsage()
    {
        $configPath = $this->createConfigFile('config.yml',
<<<EOF
drupal:
  settings:
    databases:
      default:
        default:
          database: drupal_db
          username: drupal_user
          password: drupal_pass
          driver: mysql
    conf:
      project_dir: %PROJECT_DIR
EOF
        );

        $this->compileConfig($configPath);

        $this->assertSettingsVarEquals('conf', array(
            'project_dir' => PROJECT_DIR
        ));
    }

    public function testThatCompilesSettingsWithIncludes()
    {
        $phpIncludeFilePath1 = $this->createConfigFile('include_settings_1.php',
<<<EOF
<?php
\$usefulKey = 88;
\$anotherKey = 45;
EOF
        );

        $phpIncludeFilePath2 = $this->createConfigFile('include_settings_2.php',
<<<EOF
<?php
\$usefulKey = 88;
\$anotherKey = 44; // should override previous value
EOF
        );

        $phpRequireFilePath3 = $this->createConfigFile('include_settings_3.php',
<<<EOF
<?php
\$animal = 'elephant';
EOF
        );

        $configPath = $this->createConfigFile('config.yml',
<<<EOF
drupal:
  settings:
    databases:
      default:
        default:
          database: drupal_db
          username: drupal_user
          password: drupal_pass
          driver: mysql
  include:
    include:
      - $phpIncludeFilePath1
      - $phpIncludeFilePath2
    require:
      - $phpRequireFilePath3
EOF
        );

        $this->compileConfig($configPath);

        $this->assertSettingsVarEquals('usefulKey', 88);
        $this->assertSettingsVarEquals('anotherKey', 44);
        $this->assertSettingsVarEquals('animal', 'elephant');
    }
}
