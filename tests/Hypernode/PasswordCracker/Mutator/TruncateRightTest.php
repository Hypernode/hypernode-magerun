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
 * Class TruncateRightTest
 * @package Hypernode\Magento\PasswordCracker\Mutator
 */
class TruncateRightTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new TruncateRight($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array(']', 'p@ssW0rd', 'p@ssW0r'),
            array(']', 'ABC', 'AB'),
            array(']', 'aBC', 'aB'),
            array(']', '1abc', '1ab'),
            array(']', '@Bc@', '@Bc'),
        );
    }
}
