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

class Reverse extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 'r';
    }

    public static function getLength()
    {
        return 1;
    }

    public static function validate($mutator)
    {
        return ($mutator === self::getIdentifier());
    }

    public function mutate($input)
    {
        return strrev($input);
    }
}
