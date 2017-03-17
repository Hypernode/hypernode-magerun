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
 * Class RuleIterator
 * @package Hypernode\PasswordCracker
 */
class RuleIterator extends \FilterIterator
{
    /**
     * @return bool
     */
    public function accept()
    {
        $value = trim($this->getInnerIterator()->current());
        if (preg_match('~^(#|$)~', $value)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function current()
    {
        return trim(parent::current());
    }
}
