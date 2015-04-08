<?php

namespace Tests\Drupal\Settings;

use Drupal\Settings\Compiler;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    private $testUid;

    /**
     * @var Compiler
     */
    private $compiler;

    public function setUp()
    {
    }

    public function tearDown()
    {
        if ($this->testUid) {
            $this->removeDir($this->getTestTmpDir());
            $this->unsetTestUid();
        }
    }

    protected function createConfigFile($name = 'config.yml', $content = '')
    {
        $path = $this->getTestTmpDir() . str_replace('//', '/', '/'.$name);
        $this->createDir(dirname($path));
        if (false == file_put_contents($path, $content)) {
            throw new \RuntimeException(sprintf('Could not create config file "%s"', $path));
        }

        return $path;
    }

    private function getTestTmpDir()
    {
        return TMP_DIR . '/' . $this->getTestUid();
    }


    private function getTestUid()
    {
        return $this->testUid ?: $this->testUid = $this->getName() . '__' . (string) microtime(true);
    }

    private function unsetTestUid()
    {
        $this->testUid = null;
    }

    protected function getSettingsTargetPath()
    {
        return $this->getTestTmpDir() . '/settings.php';
    }


    private function createDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0766, true);
        }
    }

    private function removeDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $path) {
            @unlink($path) || rmdir($path);
        }
        rmdir($dir);
    }

    protected function assertSettingsVarEquals($varName, $value)
    {
        $this->assertFileExists($this->getSettingsTargetPath());

        include $this->getSettingsTargetPath();
        $this->assertEquals($value, $$varName, sprintf('Check settings variable "$%s" equality', $varName));
    }

    protected function assertIniSettingEquals($ini, $value)
    {
        $this->assertFileExists($this->getSettingsTargetPath());

        require $this->getSettingsTargetPath();

        $this->assertEquals($value, ini_get($ini), sprintf('Check ini setting "%s" equality', $ini));
    }

    protected function compileConfig($configPath)
    {
        if (null === $this->compiler) {
            $this->compiler = new Compiler($configPath);
        }

        $this->compiler->write($this->getSettingsTargetPath());
    }

}
