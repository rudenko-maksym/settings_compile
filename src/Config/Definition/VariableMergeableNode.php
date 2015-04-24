<?php

namespace Drupal\Settings\Config\Definition;

use Symfony\Component\Config\Definition\VariableNode;

class VariableMergeableNode extends VariableNode
{

    /**
     * Uses array_replace_recursive(), if both $leftSide and
     * $rightSide are of type array
     *
     * {@inheritdoc}
     */
    protected function mergeValues($leftSide, $rightSide)
    {
        if (is_array($leftSide) && is_array($rightSide)) {
            return array_replace_recursive($leftSide, $rightSide);
        }

        return parent::mergeValues($leftSide, $rightSide);
    }
}
