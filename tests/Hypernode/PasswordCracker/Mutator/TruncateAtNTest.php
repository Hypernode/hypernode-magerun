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

class TruncateAtNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new TruncateAtN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array("'6", 'p@ssW0rd', 'p@ssW0'),
            array("'2", 'ABC', 'AB'),
            array("'4", 'abcdefghij', 'abcd'),
            array("'3", '1234567890', '123'),
            array("'A", 'abcdefghijklmnopqrstu', 'abcdefghij'),
        );
    }
}
