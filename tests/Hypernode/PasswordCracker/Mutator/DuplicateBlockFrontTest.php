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

class DuplicateBlockFrontTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateBlockFront($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('y2', 'p@ssW0rd', 'p@p@ssW0rd'),
            array('y1', 'ABC', 'AABC'),
            array('y2', '112233', '11112233'),
            array('y3', '1', '11'),
        );
    }
}
