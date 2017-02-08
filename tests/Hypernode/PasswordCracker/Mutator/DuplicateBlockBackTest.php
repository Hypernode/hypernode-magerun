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

class DuplicateBlockBackTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateBlockBack($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('Y2', 'p@ssW0rd', 'p@ssW0rdrd'),
            array('Y1', 'ABC', 'ABCC'),
            array('Y2', '112233', '11223333'),
            array('Y3', '1', '11'),
        );
    }
}
