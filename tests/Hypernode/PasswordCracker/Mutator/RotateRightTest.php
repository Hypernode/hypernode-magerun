<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\PasswordCracker\Mutator;

/**
 * Class RotateRightTest
 * @package Hypernode\Magento\PasswordCracker\Mutator
 */
class RotateRightTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new RotateRight($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('}', 'p@ssW0rd', 'dp@ssW0r'),
            array('}', 'ABC', 'CAB'),
            array('}', 'aBC', 'CaB'),
            array('}', 'a', 'a'),
            array('}', '@Bc@', '@@Bc'),
        );
    }
}
