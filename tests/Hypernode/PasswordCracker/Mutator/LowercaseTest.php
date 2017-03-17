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
 * Class LowercaseTest
 * @package Hypernode\Magento\PasswordCracker\Mutator
 */
class LowercaseTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Lowercase($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('l', 'p@ssW0rd', 'p@ssw0rd'),
            array('l', 'ABC', 'abc'),
            array('l', 'aBC', 'abc'),
            array('l', '1abc', '1abc'),
            array('l', '@Bc', '@bc'),
        );
    }
}