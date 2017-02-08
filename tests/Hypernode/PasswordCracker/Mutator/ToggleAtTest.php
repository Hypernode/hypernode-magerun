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

class ToggleAtTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new ToggleAt($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('T3', 'p@ssW0rd', 'p@sSW0rd'),
            array('T1', 'ABC', 'AbC'),
            array('T6', 'abc', 'abc'),
            array('T1', '12ABC', '12ABC'),
            array('T1', 'abc', 'aBc'),
        );
    }
}
