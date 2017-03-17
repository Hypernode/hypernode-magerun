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

/**
 * Class FileIteratorTest
 * @package Hypernode\PasswordCracker
 */
class MutatedWordIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessCalledOnElements()
    {
        $rule = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->setMethods(['process'])
            ->getMock();

        $rule->expects($this->at(0))
            ->method('process')
            ->with($this->equalTo('abc'))
            ->will($this->returnValue('abc'));

        $rule->expects($this->at(1))
            ->method('process')
            ->with($this->equalTo('def'))
            ->will($this->returnValue('def'));

        $mutatedWordIterator = new MutatedWordIterator(
            new \ArrayIterator(array('abc', 'def')),
            new \ArrayIterator(array($rule))
        );

        foreach ($mutatedWordIterator as $word) {

        }
    }
}
