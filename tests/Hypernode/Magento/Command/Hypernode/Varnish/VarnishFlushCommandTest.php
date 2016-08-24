<?php

namespace Hypernode\Magento\Command\Hypernode\Varnish;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class VarnishFlushCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new VarnishFlushCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
            ->find('hypernode:varnish:flush');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:varnish:flush', $command->getName());
    }

    public function isTurpentineEnabled()
    {
        return \Mage::helper('core')->isModuleEnabled('Nexcessnet_Turpentine');
    }

    public function testExecute()
    {
        $command = $this->getCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $result = $commandTester->getDisplay();

        if (!$this->isTurpentineEnabled()) {
            $this->assertRegExp('/^(.*?(Turpentine is not enabled or installed.)[^$]*)$/m', $result);
        } else {

            $command = $this->getCommand();

            $commandTester = new CommandTester($command);
            $commandTester->execute([]);

            $result = $commandTester->getDisplay();

            $this->assertRegExp('/^(.*?(Flushed cached varnish URL\'s)[^$]*)$/m',$result);

        }
    }

}
