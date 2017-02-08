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

class ReverseTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Reverse($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('r', 'p@ssW0rd', 'dr0Wss@p'),
            array('r', 'ABC', 'CBA'),
            array('r', 'aBC', 'CBa'),
            array('r', 'abcba', 'abcba'),
            array('r', '@Bc@', '@cB@'),
        );
    }
}
