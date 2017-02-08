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

class DuplicateAllTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DuplicateAll($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('q', 'p@ssW0rd', 'pp@@ssssWW00rrdd'),
            array('q', 'ABC', 'AABBCC'),
            array('q', '123', '112233'),
            array('q', '1', '11'),
        );
    }
}
