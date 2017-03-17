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
 * Class Credential
 * @package Hypernode\PasswordCracker
 */
class Credential
{
    /** @var  string */
    protected $hash;
    /** @var  string */
    protected $password;
    /** @var  bool */
    protected $cracked;
    /** @var  string */
    protected $id;

    /**
     * Credential constructor.
     * @param string $hash
     * @param null|string $id
     */
    public function __construct($hash, $id = null)
    {
        $this->setHash($hash);
        $this->setId($id);
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return bool
     */
    public function isCracked()
    {
        return $this->cracked;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->cracked = true;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
