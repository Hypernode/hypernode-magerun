<?php
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 7-5-16
 * Time: 12:56
 */

namespace Hypernode\Magento\Command\Hypernode\Config;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

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
