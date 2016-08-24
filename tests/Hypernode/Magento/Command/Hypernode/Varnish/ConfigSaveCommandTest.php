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
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ConfigSaveCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Varnish
 */
class ConfigSaveCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new ConfigSaveCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
            ->find('hypernode:varnish:config-save');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:varnish:config-save', $command->getName());
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

          $vcl = "false";
            $vclFile = $this->getApplication()->getMagentoRootFolder()."/var/default.vcl";

            if(file_exists($vclFile)){
                $vcl = file_get_contents($vclFile);
                $vcl = (string) $vcl;
            }

            $this->assertRegExp('/^(.*?(\.port = \"8080\"\;)[^$]*)$/m',$vcl);
            $this->assertRegExp('/^(.*?(\.host = \"varnish\"\;)[^$]*)$/m',$vcl);
            $this->assertRegExp('/^(.*?(backend admin \{)[^$]*)$/m',$vcl);
            $this->assertRegExp('/^(.*?(backend default \{)[^$]*)$/m',$vcl);
        }
    }

}
