<?php

namespace Hypernode\Magento\Command\System\Patches;

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
        /** @todo Create a mock for curl */
//        $commandTester = new CommandTester($this->command);
//        $commandTester->execute([]);
    }

}
