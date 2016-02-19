<?php

namespace Hypernode\Magento\Command\System\Patches;

use Symfony\Component\Console\Tester\CommandTester;
use N98\Magento\Command\PHPUnit\TestCase;

class ListCommandTest extends TestCase
{

    public function testExecute()
    {
        $application = $this->getApplication();

//        $application->add(new ListCommand());
//        $command = $this->getApplication()->find('sys:patches:list');

//        $commandTester = new CommandTester($command);
//        $commandTester->execute(array('command' => $command->getName()));
//
//        $this->assertRegExp('/Magento System Information/', $commandTester->getDisplay());
//        $this->assertRegExp('/Install Date/', $commandTester->getDisplay());
//        $this->assertRegExp('/Crypt Key/', $commandTester->getDisplay());
//
//        // Settings argument
//        $commandTester->execute(
//                array(
//                        'command' => $command->getName(),
//                        'key'     => 'version',
//                )
//        );
//        $this->assertRegExp('/\d+\.\d+\.\d+\.\d+/', $commandTester->getDisplay());
    }

}
