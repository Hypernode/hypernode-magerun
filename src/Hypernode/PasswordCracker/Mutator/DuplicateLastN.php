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

class DuplicateLastN extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 'Z';
    }

    public static function getLength()
    {
        return 2;
    }

    public static function validate($mutator)
    {
        return preg_match('~^' . preg_quote(self::getIdentifier(), '~'). '\d$~', $mutator);
    }

    public function mutate($input)
    {
        return $input . str_repeat(substr($input, -1), $this->getArg(1));
    }
}
