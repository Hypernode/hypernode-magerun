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

class Replace extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 's';
    }

    public static function getLength()
    {
        return 3;
    }

    public static function validate($mutator)
    {
        return preg_match('~^' . preg_quote(self::getIdentifier(), '~'). '..$~', $mutator);
    }

    public function mutate($input)
    {
        return str_replace($this->getArg(1), $this->getArg(2), $input);
    }
}
