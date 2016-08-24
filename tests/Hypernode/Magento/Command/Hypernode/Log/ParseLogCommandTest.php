<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Config;

use Hypernode\Curl;
use N98\Magento\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ParseLogCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Config
 */
class ParseLogCommandTest extends TestCase
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
                ->find('hypernode:log:parse');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:log:parse', $command->getName());
    }

    public function testOptions()
    {
        $command = $this->getCommand();
        $this->assertEquals('top', $command->getDefinition()
            ->getArgument('top')->getName());
        $this->assertEquals('log', $command->getDefinition()
                ->getOption('log')->getName());
    }


    public function testExecute()
    {
        $command = $this->getCommand();

        \Mage::log('hypernode parse log test',null,'hypernode.log',true);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'top' => 20,
                '--log' => 'hypernode.log'
            )
        );

        $result = $commandTester->getDisplay();

        $this->assertRegExp('/^(.*?(hypernode parse log test)[^$]*)$/m',$result);
    }

}
