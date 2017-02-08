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
 * Class CredentialTest
 * @package Hypernode\PasswordCracker
 */
class CredentialTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleConstructorArgument()
    {
        $cred = new Credential('hash');

        $this->assertEquals('hash', $cred->getHash());
        $this->assertEquals(null, $cred->getId());
    }

    public function testTwoConstructorArguments()
    {
        $cred = new Credential('hash', 'password');

        $this->assertEquals('hash', $cred->getHash());
        $this->assertEquals('password', $cred->getId());
    }

    public function testSetCracked()
    {
        $cred = new Credential('hash');
        $this->assertEquals(false, $cred->isCracked());

        $cred->setPassword('password');
        $this->assertEquals(true, $cred->isCracked());
    }

    public function testGetterSetterPairs()
    {
        $cred = new Credential('contructHash', 'constructId');
        $cred->setHash('hash');
        $cred->setId('id');
        $cred->setPassword('password');

        $this->assertEquals('hash', $cred->getHash());
        $this->assertEquals('id', $cred->getId());
        $this->assertEquals('password', $cred->getPassword());
    }
}
