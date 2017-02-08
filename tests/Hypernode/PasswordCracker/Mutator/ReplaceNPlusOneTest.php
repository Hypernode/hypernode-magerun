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

class ReplaceNPlusOneTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new ReplaceNPlusOne($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        // @todo - .0 replaces with out of bounds should return original

        return array(
            array('.1', 'p@ssW0rd', 'psssW0rd'),
            array('.1', 'ABC', 'ACC'),
            array('.2', '112233', '112233'),
            arraY('.2', 'ABC', 'ABC'),
            array('.A', 'abcdefghijklmnopqrstuvwxyz', 'abcdefghijllmnopqrstuvwxyz'),
        );
    }
}
