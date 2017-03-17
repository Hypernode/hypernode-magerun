<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Crack;

use N98\Magento\Command\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AdminPasswordCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Crack
 */
class AdminPasswordCommandTest extends TestCase
{
    public function setUp()
    {
        $application = $this->getApplication();
        $command = new AdminPasswordCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
            ->find('hypernode:crack:admin-passwords');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:crack:admin-passwords', $command->getName());
    }
}

