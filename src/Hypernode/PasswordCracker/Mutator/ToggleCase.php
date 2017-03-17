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

class ToggleCase extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 't';
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
        $letters = str_split($input);
        $str = '';
        foreach ($letters as $l) {
            $str .= ($l === strtoupper($l) ? strtolower($l) : strtoupper($l));
        }

        return $str;
    }
}
