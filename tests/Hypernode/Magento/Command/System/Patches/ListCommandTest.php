<?php

namespace Hypernode\Magento\Command\System\Patches;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

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
                ->find('sys:patches:list');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('sys:patches:list', $command->getName());
    }

    public function testAliases()
    {
        // Backwards compatible
        $command = $this->getCommand();
        $this->assertArraySubset(['sys:info:patches'], $command->getAliases());
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

        $curl->expects($this->any())
                ->method('get');

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
