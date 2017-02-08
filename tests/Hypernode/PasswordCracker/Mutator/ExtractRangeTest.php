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

class ExtractRangeTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new ExtractRange($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('x04', 'p@ssW0rd', 'p@ss'),
            array('x12', 'ABC', 'BC'),
            array('xA1', '112233', '112233'),
            array('x1A', '1', '1'),
        );
    }
}
