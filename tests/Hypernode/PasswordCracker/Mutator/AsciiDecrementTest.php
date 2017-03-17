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

class AsciiDecrementTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new AsciiDecrement($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('-1', 'p@ssW0rd', 'p?ssW0rd'),
            array('-1', 'ABC', 'AAC'),
            array('-2', '123ABC', '122ABC'),
            array('-5', 'abc', 'abc'),
            array('-A', 'abcdefghijklmnopqrstuvwxyz', 'abcdefghijjlmnopqrstuvwxyz'),
        );
    }
}
