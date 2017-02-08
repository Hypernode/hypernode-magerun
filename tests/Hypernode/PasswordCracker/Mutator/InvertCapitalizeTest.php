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

class InvertCapitalizeTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new InvertCapitalize($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('C', 'p@ssW0rd', 'p@SSW0RD'),
            array('C', 'ABC', 'aBC'),
            array('C', '123abc', '123ABC'),
            array('C', 'abc', 'aBC'),
        );
    }
}
