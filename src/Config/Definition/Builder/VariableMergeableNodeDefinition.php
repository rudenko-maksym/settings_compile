<?php

namespace Drupal\Settings\Config\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;
use Drupal\Settings\Config\Definition\VariableMergeableNode;

class VariableMergeableNodeDefinition extends VariableNodeDefinition
{
    /**
     * Instantiate a Node.
     *
     * @return VariableNode The node
     */
    protected function instantiateNode()
    {
        return new VariableMergeableNode($this->name, $this->parent);
    }
}
