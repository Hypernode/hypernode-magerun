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

class ExtractRange extends AbstractMutator
{
    public static function getIdentifier()
    {
        return 'x';
    }

    public static function getLength()
    {
        return 3;
    }

    public static function validate($mutator)
    {
        return preg_match('~^' . preg_quote(self::getIdentifier(), '~'). '\d\d$~', $mutator);
    }

    public function mutate($input)
    {
        $i = $this->getPositionArg(1);
        if (! $this->validatePosition($i, $input)) {
            return $input;
        }

        return substr($input, $i, $this->getPositionArg(2));
    }
}
