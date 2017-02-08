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

use N98\Magento\Command\TestCase;

class PrependMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepend()
    {
        $memorize = new Memorize('M');
        $mutator  = new PrependMemory('6');
        $input    = 'p@ssW0rd';

        $memorize->mutate($input);

        $this->assertEquals($mutator->mutate('abc'), 'p@ssW0rdabc');
    }
}
