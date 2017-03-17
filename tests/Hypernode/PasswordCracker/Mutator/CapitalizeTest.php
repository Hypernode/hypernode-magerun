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

class CapitalizeTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Capitalize($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('c', 'p@ssW0rd', 'P@ssw0rd'),
            array('c', 'ABC', 'Abc'),
            array('c', '123ABC', '123abc'),
            array('c', 'abc', 'Abc'),
        );
    }
}
