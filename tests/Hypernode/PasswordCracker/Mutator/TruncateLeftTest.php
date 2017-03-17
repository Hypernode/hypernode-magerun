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

class TruncateLeftTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new TruncateLeft($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array(']', 'p@ssW0rd', '@ssW0rd'),
            array(']', 'ABC', 'BC'),
            array(']', 'aBC', 'BC'),
            array(']', '1abc', 'abc'),
            array(']', '@Bc@', 'Bc@'),
        );
    }
}
