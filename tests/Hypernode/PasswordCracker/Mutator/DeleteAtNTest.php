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

class DeleteAtNTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new DeleteAtN($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('D3', 'p@ssW0rd', 'p@sW0rd'),
            array('D1', 'ABC', 'AC'),
            array('D8', '123', '123'),
            array('D2', '1234567890abc', '124567890abc'),
        );
    }
}
