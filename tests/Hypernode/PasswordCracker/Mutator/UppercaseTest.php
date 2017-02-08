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
 * Class UppercaseTest
 * @package Hypernode\Magento\PasswordCracker\Mutator
 */
class UppercaseTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Uppercase($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('u', 'p@ssW0rd', 'P@SSW0RD'),
            array('u', 'aBC', 'ABC'),
            array('u', '1abc', '1ABC'),
            array('u', '@Bc', '@BC'),
        );
    }
}
