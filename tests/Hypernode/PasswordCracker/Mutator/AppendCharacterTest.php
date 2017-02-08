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

class AppendCharacterTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new AppendCharacter($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('$1', 'p@ssW0rd', 'p@ssW0rd1'),
            array('$a', 'ABC', 'ABCa'),
            array('$1', 'aBC', 'aBC1'),
            array('$%', 'a', 'a%'),
        );
    }
}
