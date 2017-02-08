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

class ExtractMemory extends Memorize
{
    public static function getIdentifier()
    {
        return 'X';
    }

    public static function getLength()
    {
        return 4;
    }

    public static function validate($mutator)
    {
        return preg_match('~^' . preg_quote(self::getIdentifier(), '~'). '\d\d\d$~', $mutator);
    }

    public function mutate($input)
    {
        // @todo - validate ranges
        $extractFrom   = $this->getArg(1);
        $extractLength = $this->getArg(2);
        $insertAt      = $this->getArg(3);
        $start  = substr($input, 0, $insertAt);
        $end    = substr($input, $insertAt);
        $insert = substr(self::$buffer, $extractFrom, $extractLength);

        return $start . $insert . $end;
    }
}
