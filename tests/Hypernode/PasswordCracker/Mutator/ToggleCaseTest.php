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

class ToggleCaseTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new ToggleCase($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('t', 'p@ssW0rd', 'P@SSw0RD'),
            array('t', 'ABC', 'abc'),
            array('t', 'abc', 'ABC'),
            array('t', 'AbC', 'aBc'),
            array('t', 'AbC123', 'aBc123'),
        );
    }
}
