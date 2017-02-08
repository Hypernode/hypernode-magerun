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

abstract class AbstractMutator implements MutatorInterface
{
    protected $args;

    public function __construct($args)
    {
        $this->args = str_split($args);
    }

    public function getArg($index)
    {
        return isset($this->args[$index]) ?
            $this->args[$index] :
            false;
    }

    public function getPositionArg($index)
    {
        $indexes = array(
            1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8,
            9 => 9, 'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14,
            'F' => 15, 'G' => 16, 'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20,
            'L' => 21, 'M' => 22, 'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26,
            'R' => 27, 'S' => 28, 'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32,
            'X' => 33, 'Y' => 34, 'Z' => 35,
        );

        $value = isset($this->args[$index]) ?
            $this->args[$index] :
            false;

        return isset($indexes[$value]) ?
            $indexes[$value] :
            false;
    }

    public function unichr($i)
    {
        // @todo - I'm not sure this is 100% right
        return @iconv('UCS-4LE', 'UTF-8', pack('V', $i));
    }

    public function uniord($s)
    {
        // @todo - I'm not sure this is 100% right
        return @unpack('V', iconv('UTF-8', 'UCS-4LE', $s))[1];
    }

    public function validatePosition($position, $input)
    {
        return ($position >= 0 && $position < strlen($input));
    }
}
