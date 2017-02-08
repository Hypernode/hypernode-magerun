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

class ExtractMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAppend()
    {
        $memorize = new Memorize('M');
        $mutator  = new ExtractMemory('X428');
        $input    = 'p@ssW0rd';

        $memorize->mutate($input);

        $this->assertEquals($mutator->mutate($input), 'p@ssW0rdW0');
    }

    public function testExtractOutOfBounds()
    {
        $memorize = new Memorize('M');
        $mutator  = new ExtractMemory('X928');
        $input    = 'p@ssW0rd';

        $memorize->mutate($input);

        $this->assertEquals($mutator->mutate($input), 'p@ssW0rd');
    }

    public function testExtractLengthOutOfBounds()
    {
        $memorize = new Memorize('M');
        $mutator  = new ExtractMemory('X488');
        $input    = 'p@ssW0rd';

        $memorize->mutate($input);

        $this->assertEquals($mutator->mutate($input), 'p@ssW0rdW0rd');
    }

    public function testInsertOutOfBounds()
    {
        $memorize = new Memorize('M');
        $mutator  = new ExtractMemory('X429');
        $input    = 'p@ssW0rd';

        $memorize->mutate($input);

        $this->assertEquals($mutator->mutate($input), 'p@ssW0rdW0');
    }
}
