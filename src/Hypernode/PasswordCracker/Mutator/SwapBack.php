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

class SwapBack extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 'K';
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
        $last = substr($input, -1, 1);
        $secondLast = substr($input, -2, 1);

        return substr($input, 0, -2) . $last . $secondLast;
    }
}
