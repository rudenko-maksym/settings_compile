<?php

namespace Tests\Drupal\Settings\Config\Definition\VariableMergeableTest;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class VariableMergeableTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->nodeBuilder = new NodeBuilder();
        $this->nodeBuilder
            ->setNodeClass('variableMergeable', 'Drupal\\Settings\\Config\\Definition\\Builder\\VariableMergeableNodeDefinition');
    }

    public function testUsesArrayReplaceRecursiveFunctionForArrayWithArrayMerge()
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array', $this->nodeBuilder)
                ->children()
                    ->node('conf', 'variableMergeable')->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = array(
            'conf' => array(
                'project_dir' => '%PROJECT_DIR',
                'env' => array(
                    'ENV_1' => 'value_3',
                    'ENV_2' => 'value_2'
                ),
                'str_with_array' => 'foo',
                'array_with_str' => array('bar')
            )
        );

        $b = array(
            'conf' => array(
                'debug' => true,
                'env' => array(
                    'ENV_1' => 'value_1',
                ),
                'str_with_array' => array('bar'),
                'array_with_str' => 'foo'
            )
        );

        $this->assertEquals(array(
            'conf' => array(
                'project_dir' => '%PROJECT_DIR',
                'debug' => true,
                'env' => array(
                    'ENV_1' => 'value_1',
                    'ENV_2' => 'value_2'
                ),
                'str_with_array' => array('bar'),
                'array_with_str' => 'foo'
            )
        ), $tree->merge($a, $b));
    }

    public function testUsesDefaultMergeMethodForOtherCombinations()
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array', $this->nodeBuilder)
                ->children()
                    ->node('conf', 'variableMergeable')->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = array(
            'conf' => 'value 1'
        );

        $b = array(
            'conf' => array('value in array')
        );

        $this->assertEquals(array(
            'conf' => array('value in array')
        ), $tree->merge($a, $b));
    }
}
