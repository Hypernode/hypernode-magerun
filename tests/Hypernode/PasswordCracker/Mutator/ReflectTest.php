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

class ReflectTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Reflect($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('f', 'p@ssW0rd', 'p@ssW0rddr0Wss@p'),
            array('f', '123', '123321'),
            array('f', '1', '11'),
        );
    }
}
