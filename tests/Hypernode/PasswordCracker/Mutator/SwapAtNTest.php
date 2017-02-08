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

class SwapAtNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new SwapAtN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('*34', 'p@ssW0rd', 'p@sWs0rd'),
            array('*12', 'abcdef', 'acbdef'),
            array('*23', 'abcdef', 'abdcef'),
            array('*89', 'abcdef', 'abcdef'),
            array('*A5', 'abcdefghijklmnopqrstuvwxyz', 'abcdekghijflmnopqrstuvwxyz'),
        );
    }
}
