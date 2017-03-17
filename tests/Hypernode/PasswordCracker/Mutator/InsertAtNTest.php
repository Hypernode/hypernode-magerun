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

class InsertAtNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new InsertAtN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('i4!', 'p@ssW0rd', 'p@ss!W0rd'),
            array('i12', 'ABC', 'A2BC'),
            array('i1A', '112233', '1A12233'),
            array('iA1', '1', '11'),
        );
    }
}
