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

class ReplaceNMinusOneTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new ReplaceNMinusOne($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        // @todo - .0 replaces with -1 char, out of bounds should return original
        return array(
            array(',1', 'p@ssW0rd', 'ppssW0rd'),
            array(',1', 'ABC', 'AAC'),
            array(',0', 'ABC', 'ABC'),
            array(',A', 'abcdefghijklmnopqrstuvwxyz', 'abcdefghijjlmnopqrstuvwxyz'),
            array(',2', '1234', '1224'),
        );
    }
}
