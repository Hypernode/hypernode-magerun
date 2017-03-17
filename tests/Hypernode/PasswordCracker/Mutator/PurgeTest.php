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

class PurgeTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Purge($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('@s', 'p@ssW0rd', 'p@W0rd'),
            array('@A', 'ABC', 'BC'),
            array('@A', 'AAABCE', 'BCE'),
            array('@1', '123456', '23456'),
            array('@X', 'abcdef', 'abcdef'),
        );
    }
}
