<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 *
 * @todo finish execute test for at least hypernode internal usage
 */

namespace Hypernode\Magento\Command\Hypernode\Performance;


use N98\Magento\Command\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class PerformanceCommandTest
 * @package Hypernode\Magento\Command\Hypernode\Performance
 */
class PerformanceCommandTest extends TestCase
{

    public function setUp()
    {
        $application = $this->getApplication();
        $command = new PerformanceCommand();

        $application->add($command);
    }

    public function getCommand()
    {
        return $this->getApplication()
            ->find('hypernode:performance');
    }

    public function testName()
    {
        $command = $this->getCommand();
        $this->assertEquals('hypernode:performance', $command->getName());
    }

    public function testOptions()
    {
        $command = $this->getCommand();
        $this->assertEquals('sitemap', $command->getDefinition()
            ->getOption('sitemap')->getName());
        $this->assertEquals('current-url', $command->getDefinition()
            ->getOption('current-url')->getName());
        $this->assertEquals('compare-url', $command->getDefinition()
            ->getOption('compare-url')->getName());
        $this->assertEquals('silent', $command->getDefinition()
            ->getOption('silent')->getName());
        $this->assertEquals('format', $command->getDefinition()
            ->getOption('format')->getName());
        $this->assertEquals('limit', $command->getDefinition()
            ->getOption('limit')->getName());
        $this->assertEquals('totaltime', $command->getDefinition()
            ->getOption('totaltime')->getName());
    }


//    public function testExecute()
//    {
//        $command = $this->getCommand();
//
//        $commandTester = new CommandTester($command);
//        $commandTester->execute([]);
//
//        $result = $commandTester->getDisplay();
//
//        $this->assertRegExp('/^(.*?(Store)[^$]*)$/m',$result);
//
//    }

}
