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
use N98\Magento\Modules;
use N98\Magento\Command\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ListUpdatesCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Modules
 */
class ListUpdatesCommandTest extends TestCase
{

    /**
     * @var Modules $modules
     */
    public $modules;

    public function setUp()
    {
        $this->modules = new Modules();
        $application = $this->getApplication();
        /** @var ListUpdatesCommand $command */
        $command = new ListUpdatesCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
            ->find('hypernode:modules:list-updates');
    }

    public function testModules()
    {
        $modules = $this->modules;
        $this->assertInstanceOf(Modules::class, $modules);
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
        $this->assertEquals('only-active', $command->getDefinition()
            ->getOption('only-active')->getName());
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

    public function testOnlyActive()
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
                ],
                [
                    'latest' => '1.6.0.0',
                    'current' => '1.6.0.0',
                    'name' => 'Phoenix_Moneybookers',
                ]
            ]
        ]);

        // disable module
        $disableCommand = $this->getApplication()->find('dev:module:disable');

        $disableExecute = new CommandTester($disableCommand);

        $disableExecute->execute(array(
            'command' => $disableCommand->getName(),
            '--codepool' => 'community',
            'moduleName' => 'Phoenix_Moneybookers'
        ));

        // Set mock
        $command->setCurl($curl);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--only-active' => true,
            )
        );

        $result = $commandTester->getDisplay();

        $this->assertNotContains('(inactive)', $result);
    }

    public function testFaultyModulesConfig()
    {
        /** @var \Hypernode\Magento\Command\Hypernode\Modules\ListUpdatesCommand $command */
        $command = $this->getCommand();
        $rootDir = $this->getTestMagentoRoot();

        $testModuleVendor = 'Frosit';
        $testModuleName = 'Test';
        $testModuleNamespace = $testModuleVendor . '_' . $testModuleName;
        $testModuleCodePool = 'local';
        $testModuleVersion = '0.1.0';

        $testEtcFile = $rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $testModuleNamespace.'.xml';
        $testModuleDir = $rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . $testModuleCodePool . DIRECTORY_SEPARATOR . $testModuleVendor . DIRECTORY_SEPARATOR . $testModuleName;
        $testModuleConfigFile = $testModuleDir . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'config.xml';

        $etcFileTemplate = '<?xml version="1.0"?>
<config>
    <modules>
        <'.$testModuleNamespace.'>
            <active>false</active>
            <codePool>' . $testModuleCodePool . '</codePool>
        </'.$testModuleNamespace.'>
    </modules>
</config>';

        $moduleConfigFileTemplate = '<?xml version="1.0"?>
<config>
    <modules>
        <'.$testModuleNamespace.'>
            <version>'.$testModuleVersion.'</version>
        </'.$testModuleNamespace.'>
    </modules>
</config>';

        // Create directory if not exists
        if (!file_exists($testModuleDir . DIRECTORY_SEPARATOR . 'etc')) {
            mkdir($testModuleDir . DIRECTORY_SEPARATOR . 'etc', 0755, true);
        }

        // Place config files
        if(file_put_contents($testEtcFile, $etcFileTemplate) && file_put_contents($testModuleConfigFile,$moduleConfigFileTemplate)){
            if(file_exists($testModuleConfigFile) && file_exists($testEtcFile)){

                \Mage::getConfig()->loadModules(); // reload config

                $version = $command->getExtensionVersion($testModuleNamespace);
                $this->assertEquals($testModuleVersion,$version,'Version does not match expected 0.1.0');
            }
        } else {
            $this->markTestSkipped('Could not place module config file(s)');
        }

        if(file_put_contents($testModuleConfigFile,'<?xml version="1.0"?><config></config>')){
            $version = $command->getExtensionVersion($testModuleNamespace);
            $this->assertNull($version,'Version was not null');
        }

        if(unlink($testModuleConfigFile)){
            $version = $command->getExtensionVersion($testModuleNamespace);
            $this->assertNull($version,'Version was not null for non-existing module config');
        }

        unlink($testEtcFile);
    }

}
