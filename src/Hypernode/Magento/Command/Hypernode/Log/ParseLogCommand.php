<?php
/**
 * Frosit Magerun Addons
 *
 * @author      Fabio Ros (Frosit) - Byte
 * @copyright   Copyright (c) 2016 Byte / Frosit
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Log;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use N98\Util\OperatingSystem;

/**
 * Class ParseLogCommand
 * @package Hypernode\Magento\Command\Hypernode\Log
 */
class ParseLogCommand extends AbstractHypernodeCommand
{

    protected $_result;

    protected function configure()
    {
        $this
            ->setName('hypernode:log:parse')
            ->setDescription('Output the top [top] most frequent lines in system.log')
            ->addArgument('top', InputArgument::OPTIONAL, "Amount of unique lines to show.", 10)
            ->addOption('log', 'l', InputOption::VALUE_OPTIONAL, "log file name [Note: it was build for system.log]", false);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return;
        }

        // this command will not run on windows.
        if (OperatingSystem::isWindows()) {
            $output->writeln('<error>This command is not compatible with windows.</error>');
            return;
        }

        $this->writeSection($output, 'Top log messages.');
        $logPath = $this->findLogPath($input->getOption('log'));

        if ($logPath) {
            $process = new Process("cut -d ' ' -f 2- " . $logPath . " | sort | uniq -c | sort -n -k 1 | tail -" . $input->getArgument('top') . "");

            $process->run(function ($type, $buffer) {

                if (Process::STATUS_READY) {
                    $this->_result = $buffer;
                }
            });

            $results = array_reverse(array_filter(explode("\n", $this->_result)));

            $i = 1;
            foreach ($results as $result) {
                $output->writeln('<comment>' . $i . '.</comment>' . " : " . $result);
                $i++;
            }

        } else {
            $output->writeln('<error>Could not find the path to the system.log</error>');
        }
    }

    /**
     * Finds the path to the system.log
     * @param bool $log
     * @return bool|string
     */
    protected function findLogPath($log = false)
    {
        if (!$log) {
            $log = "system.log";
        }
        $main = $this->_magentoRootFolder . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . $log;
        if (file_exists($main)) {
            return $main;
        } else {
            return false;
        }
    }

}