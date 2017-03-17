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
 * Class CrackerTest
 * @package Hypernode\PasswordCracker
 */
class CrackerTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateWordProxiedToEncryptor()
    {
        $encryptor = $this->getMockBuilder(\Mage_Model_Core_Encryption::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateHash'])
            ->getMock();

        $encryptor
            ->expects(
                $this->once()
            )
            ->method('validateHash')
            ->with(
                $this->equalTo('attempt'),
                $this->equalTo('hash')
            );

        $cracker = new Cracker();
        $cracker->setEncryptor($encryptor);
        $cracker->validateWord('attempt', 'hash');
    }

    public function testPasswordSetOnCredential()
    {
        $encryptor = $this->getMockBuilder(\Mage_Model_Core_Encryption::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateHash'])
            ->getMock();

        $encryptor
            ->expects(
                $this->at(0)
            )
            ->method('validateHash')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo('hash')
            )->will(
                $this->returnValue(false)
            );

        $encryptor
            ->expects(
                $this->at(1)
            )
            ->method('validateHash')
            ->with(
                $this->equalTo('bar'),
                $this->equalTo('hash')
            )->will(
                $this->returnValue(true)
            );

        $credential = $this->getMockBuilder(Credential::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHash', 'setPassword'])
            ->getMock();

        $credential->expects($this->any())
            ->method('getHash')
            ->will(
                $this->returnValue('hash')
            );

        $credential
            ->expects($this->at(2))
            ->method('setPassword')
            ->with(
                $this->equalTo('bar')
            );

        $cracker = new Cracker();
        $cracker->setWords(new \ArrayIterator(array('foo', 'bar')));
        $cracker->setEncryptor($encryptor);
        $cracker->crack($credential);
    }
}
