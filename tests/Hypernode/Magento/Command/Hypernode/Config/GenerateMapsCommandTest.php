<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Config;

use Hypernode\Curl;
use N98\Magento\Command\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class GenerateMapsCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Config
 */
class GenerateMapsCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new GenerateMapsCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
                ->find('hypernode:maps-generate');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:maps-generate', $command->getName());
    }

    public function testAliases()
    {
        $command = $this->getCommand();
        $this->assertArraySubset([], $command->getAliases());
    }

    public function testOptions()
    {
        $command = $this->getCommand();
        $this->assertEquals('save', $command->getDefinition()
                ->getOption('save')->getName());
    }


    public function testExecute()
    {
        $command = $this->getCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $result = $commandTester->getDisplay();

        $this->assertRegExp('/^(.*?(Mage run maps for Nginx. \[Byte Hypernode\] )[^$]*)$/m',$result);
    }

}

