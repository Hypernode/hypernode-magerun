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

class DuplicateFirstNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateFirstN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('z2', 'p@ssW0rd', 'ppp@ssW0rd'),
            array('z1', 'ABC', 'AABC'),
            array('z3', '123456', '111123456'),
            array('z4', 'abc', 'aaaaabc'),
        );
    }
}
