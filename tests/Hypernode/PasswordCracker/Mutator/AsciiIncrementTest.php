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

class AsciiIncrementTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new AsciiIncrement($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('+2', 'p@ssW0rd', 'p@tsW0rd'),
            array('+1', 'ABC', 'ACC'),
            array('+2', '123ABC', '124ABC'),
            array('+5', 'abc', 'abc'),
            array('-A', 'abcdefghijklmnopqrstuvwxyz', 'abcdefghijllmnopqrstuvwxyz'),

        );
    }
}
