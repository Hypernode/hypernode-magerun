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

/**
 * Class AbstractMutatorTest
 * @package Hypernode\Magento\PasswordCracker\Mutator
 */
abstract class AbstractMutatorTest extends \PHPUnit_Framework_TestCase
{
    abstract public function getMutator($definition);

    abstract public function mutatorProvider();

    /**
     * @dataProvider mutatorProvider
     */
    public function testUMutate($definition, $input, $expected)
    {
        $mutator = $this->getMutator($definition);

        $this->assertEquals($expected, $mutator->mutate($input));
    }
}