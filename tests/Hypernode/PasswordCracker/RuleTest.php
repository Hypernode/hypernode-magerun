<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\PasswordCracker;

use Hypernode\PasswordCracker\Mutator\Uppercase;

/**
 * Class RuleTest
 * @package Hypernode\PasswordCracker
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testMutateCalledOnMutator()
    {
        $mutator1 = $this->getMockBuilder(Mutator\Nothing::class)
            ->disableOriginalConstructor()
            ->setMethods(['mutate'])
            ->getMock();

        $mutator1->expects($this->once())
            ->method('mutate')
            ->with($this->equalTo('test'));

        $rule = new Rule(array($mutator1));
        $rule->process('test');
    }

    public function testMutatedValuePassedOn()
    {
        $mutator1 = $this->getMockBuilder(Mutator\Uppercase::class)
            ->disableOriginalConstructor()
            ->setMethods(['mutate'])
            ->getMock();

        $mutator1->expects($this->once())
            ->method('mutate')
            ->with($this->equalTo('test'))
            ->will($this->returnValue('TEST'));

        $mutator2 = $this->getMockBuilder(Mutator\Nothing::class)
            ->disableOriginalConstructor()
            ->setMethods(['mutate'])
            ->getMock();

        $mutator2->expects($this->once())
            ->method('mutate')
            ->with($this->equalTo('TEST'));

        $rule = new Rule(array($mutator1, $mutator2));
        $rule->process('test');
    }
}
