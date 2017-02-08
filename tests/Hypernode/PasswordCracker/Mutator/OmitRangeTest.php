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

class OmitRangeTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new OmitRange($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('O12', 'p@ssW0rd', 'psW0rd'),
            array('O11', 'ABC', 'AC'),
            array('O13', 'AAABCE', 'ACE'),
            array('O23', 'AAABCE', 'AAE'),
            array('O91', '123456', '123456'),
            array('OA1', 'abcdef', 'abcdef'),
        );
    }
}
