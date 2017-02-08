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

class DuplicateLastNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateLastN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('Z2', 'p@ssW0rd', 'p@ssW0rddd'),
            array('Z1', 'ABC', 'ABCC'),
            array('Z2', '123', '12333'),
            array('Z3', '1234567890abc', '1234567890abcccc'),
        );
    }
}
