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

class DuplicateTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Duplicate($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('D', 'p@ssW0rd', 'p@ssW0rdp@ssW0rd'),
            array('D', 'ABC', 'ABCABC'),
            array('D', '123', '123123'),
            array('D', '1234567890abc', '1234567890abc1234567890abc'),
        );
    }
}
