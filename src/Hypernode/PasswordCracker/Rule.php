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
 * Class Rule
 * @package Hypernode\PasswordCracker
 */
class Rule
{
    /** @var array */
    private $mutators;

    /**
     * Rule constructor.
     * @param array $mutators
     */
    public function __construct(array $mutators)
    {
        $this->mutators = $mutators;
    }

    /**
     * @param $password
     * @return string
     */
    public function process($password)
    {
        foreach ($this->mutators as $mutator) {
            $password = $mutator->mutate($password);
        }

        return $password;
    }
}
