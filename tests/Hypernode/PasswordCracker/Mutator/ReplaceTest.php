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

class ReplaceTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Replace($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('ss$', 'p@ssW0rd', 'p@$$W0rd'),
            array('sA1', 'ABC', '1BC'),
            array('s1A', 'abc123', 'abcA23'),
            array('s9x', 'abcdef', 'abcdef'),
            array('scc', 'abcdef', 'abcdef'),
        );
    }
}
