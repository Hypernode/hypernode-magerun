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

class SwapFrontTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new SwapFront($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('k', 'p@ssW0rd', '@pssW0rd'),
            array('k', 'abcdef', 'bacdef'),
            array('k', '1abc', 'a1bc'),
        );
    }
}
