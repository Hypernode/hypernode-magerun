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

class NothingTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Nothing($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array(':', 'p@ssW0rd', 'p@ssW0rd'),
            array(':', 'ABC', 'ABC'),
            array(':', '123', '123'),
            array(':', '1234567890abc', '1234567890abc'),
        );
    }
}
