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

class MemorizeTest extends \PHPUnit_Framework_TestCase
{
    public function testInputNotChanged()
    {
        $mutator = new Memorize('M');
        $input   = 'abcdefghijklmnopqrstuvwxyz';

        $this->assertEquals($mutator->mutate($input), $input);
    }
}
