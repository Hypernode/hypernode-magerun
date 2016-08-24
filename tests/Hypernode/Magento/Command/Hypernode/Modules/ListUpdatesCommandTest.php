<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Modules;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ListUpdatesCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Modules
 */
class ListUpdatesCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new ListUpdatesCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
                ->find('hypernode:modules:list-updates');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:modules:list-updates', $command->getName());
    }

    public function testOptions()
    {
        $command = $this->getCommand();
        $this->assertEquals('codepool', $command->getDefinition()
                ->getOption('codepool')->getName());
        $this->assertEquals('status', $command->getDefinition()
                ->getOption('status')->getName());
        $this->assertEquals('vendor', $command->getDefinition()
                ->getOption('vendor')->getName());
        $this->assertEquals('format', $command->getDefinition()
                ->getOption('format')->getName());
    }


    public function testExecute()
    {
        $command = $this->getCommand();

        $curl = $this->getMock(Curl::class, ['post']);

        $curl->expects($this->once())
                ->method('post')
                ->with($this->equalTo($command::TOOLS_HYPERNODE_MODULE_URL));

        $curl->response = json_encode([
                'versions' => [
                        [
                            'latest' => '1.2.3',
                            'current' => '0.0.0',
                            'name' => 'Mage_Core',
                        ],
                        [
                            'latest' => '1.2.3',
                            'current' => '1.2.3',
                            'name' => 'Mage_Catalog',
                        ]
                ]
        ]);

        // Set mock
        $command->setCurl($curl);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $result = $commandTester->getDisplay();

        $this->assertRegExp('/Mage_Core.*Yes/', $result);
        $this->assertRegExp('/Mage_Catalog.*No/', $result);
    }

}

