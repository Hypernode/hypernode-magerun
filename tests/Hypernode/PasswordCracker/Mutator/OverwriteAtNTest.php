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

class OverwriteAtNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new OverwriteAtN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        // @todo - out of bounds
        return array(
            array('o3$', 'p@ssW0rd', 'p@s$W0rd'),
            array('o1x', 'ABC', 'AxC'),
            array('o2x', 'AAABCE', 'AAxBCE'),
            array('o9x', '123456', '123456'),
            array('oAx', 'abcdefghijklmnopqrstuvwxyz', 'abcdefghijxlmnopqrstuvwxyz'),
        );
    }
}
