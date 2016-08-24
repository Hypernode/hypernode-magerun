<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Patches;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ListCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Patches
 */
class ListCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new ListCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
                ->find('hypernode:patches:list');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:patches:list', $command->getName());
    }

    public function testOptions()
    {
        $command = $this->getCommand();
        $this->assertEquals('format', $command->getDefinition()
                ->getOption('format')->getName());
    }

    public function testExecute()
    {
        $command = $this->getCommand();

        $curl = $this->getMock(Curl::class, ['get']);

        $curl->expects($this->once())
                ->method('get')
                ->with($this->stringStartsWith($command::HYPERNODE_PATCH_TOOL_URL));

        $curl->response = json_encode(array(
            'required' => ["SUPEE-01234"]
        ));

        // Set mock
        $command->setCurl($curl);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertRegExp('/SUPEE\-01234/', $commandTester->getDisplay());
    }

}

