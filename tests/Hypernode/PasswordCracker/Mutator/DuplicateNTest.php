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

class DuplicateNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('p2', 'p@ssW0rd', 'p@ssW0rdp@ssW0rdp@ssW0rd'),
            array('p1', 'ABC', 'ABCABC'),
            array('p3', '123', '123123123123'),
        );
    }
}
