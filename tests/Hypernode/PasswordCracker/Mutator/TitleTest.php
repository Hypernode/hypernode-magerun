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

class TitleTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new Title($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('E', 'p@ssW0rd w0rld', 'P@ssw0rd W0rld'),
            array('E', 'abc def', 'Abc Def'),
            array('E', 'abc 1def', 'Abc 1def'),
            array('E', 'ABC', 'Abc'),
        );
    }
}
