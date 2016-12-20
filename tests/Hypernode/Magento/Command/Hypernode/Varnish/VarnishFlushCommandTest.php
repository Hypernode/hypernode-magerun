<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Varnish;

use Hypernode\Curl;
use N98\Magento\Command\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class VarnishFlushCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Varnish
 */
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
