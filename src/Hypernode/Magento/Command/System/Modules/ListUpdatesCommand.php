<?php

namespace Hypernode\Magento\Command\System\Modules;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;


class ListUpdatesCommand extends AbstractMagentoCommand
{
    const TOOLS_HYPERNODE_MODULE_URL = 'http://tools.hypernode.com/modules/magerun.json';

    protected function configure()
    {
        $this
            ->setName('sys:modules:list-updates')
            ->setDescription('Find available updates for installed modules [Hypernode]');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if ($this->initMagento()) {
            $process = new Process($_SERVER['PHP_SELF'] . ' sys:modules:list --format=json');
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $listModulesJson = $process->getOutput();
            if (!$listModulesJson) {
                $output->writeln('<error>Could not run sys:modules:list command</error>');
                exit;
            }

            $listModules = json_decode($listModulesJson, true);
            if (!$listModules) {
                $output->writeln('<error>Could not parse sys:modules:list JSON to array</error>');
                exit;
            } else {
                $modulesInfo = array();
                foreach ($listModules as $moduleInfo) {
                    $modulesInfo[$moduleInfo['Name']] = $moduleInfo;
                }
            }

            try {
                $curl = curl_init(self::TOOLS_HYPERNODE_MODULE_URL);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $listModulesJson);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($listModulesJson)
                ));

                $response = curl_exec($curl);
            } catch (Exception $e) {
                $output->writeln('<error>Could not fetch data from Hypernode platform; ' . $e->getMessage() . '</error>');
                exit(1);
            }

            $responseData = json_decode($response, true);
            if (!$responseData) {
                $output->writeln('<error>Could not fetch data from Hypernode platform.');
                exit;
            }

            $table = new Table($output);
            $table->setHeaders(array('Name', 'Code pool', 'Current version', 'Latest version', 'Newer version available?'));

            $rowsUpdates = $rowsLatest = array();
            foreach (array_pop($responseData) as $module) {
                $tableInfo = array_merge($module, $modulesInfo[$module['name']]);
                /**
                 * No inactive modules are sent back from the Hypernode platform; maybe later?
                 */
                /*if($tableInfo['Status'] == 'inactive') {
                    $tableInfo['Name'] .= ' <error>(inactive)</error>';
                }*/
                if ($tableInfo['current'] != $tableInfo['latest']) {
                    $rowsUpdates[] = array($tableInfo['Name'], $tableInfo['codePool'], $tableInfo['current'], $tableInfo['latest'], '<comment>Yes</comment>');
                } else {
                    $rowsLatest[] = array($tableInfo['Name'], $tableInfo['codePool'], $tableInfo['current'], $tableInfo['latest'], '<info>No</info>');
                }
            }
            $rows = array_merge($rowsLatest, array(new TableSeparator()), $rowsUpdates);
            $table->setRows($rows);

            $table->render();
        }
    }
}