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

class SwapBackTest extends AbstractMutatorTest
{
    public function getMutator($definition)
    {
        return new SwapBack($definition);
    }

    /**
     * @return array
     */
    public function mutatorProvider()
    {
        return array(
            array('K', 'p@ssW0rd', 'p@ssW0dr'),
            array('K', 'abcdef', 'abcdfe'),
            array('K', '1abc', '1acb'),
        );
    }
}
