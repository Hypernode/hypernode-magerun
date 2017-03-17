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

class PrependCharacterTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new PrependCharacter($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('^1', 'p@ssW0rd', '1p@ssW0rd'),
            array('^a', 'ABC', 'aABC'),
            array('^1', '123ABC', '1123ABC'),
            array('^@', 'abc', '@abc'),
        );
    }
}
