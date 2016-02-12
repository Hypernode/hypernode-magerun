<?php

namespace Hypernode\Magento\Command\System\Modules;

use N98\Magento\Command\Developer\Module\ListCommand;
use N98\Magento\Command\AbstractMagentoCommand;
use N98\Util\Console\Helper\Table\Renderer\RendererFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;


class ListUpdatesCommand extends ListCommand
{
    const TOOLS_HYPERNODE_MODULE_URL = 'http://tools.hypernode.com/modules/magerun.json';

    protected function configure()
    {
        $this
            ->setName('sys:modules:list-updates')
            ->addOption('codepool', null, InputOption::VALUE_OPTIONAL, 'Show modules in a specific codepool')
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'Show modules with a specific status')
            ->addOption('vendor', null, InputOption::VALUE_OPTIONAL, 'Show modules of a specified vendor')
            ->setDescription('Find available updates for installed modules [Hypernode]')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_OPTIONAL,
                'Output Format. One of [' . implode(',', RendererFactory::getFormats()) . ']'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        $this->initMagento();

        $this->findInstalledModules();
        $this->filterModules($input);

        $listModules = $this->infos;

        $options = JSON_FORCE_OBJECT;
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $options |= JSON_PRETTY_PRINT;
        }
        $listModulesJson = json_encode($listModules, $options);

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
            curl_setopt_array($curl, array(
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $listModulesJson,
                    CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($listModulesJson)
                    )
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

        $doFormat = $input->getOption('format') === null;

        $rowsUpdates = $rowsLatest = array();
        foreach (array_pop($responseData) as $module) {
            $tableInfo = array_merge($module, $modulesInfo[$module['name']]);

            $update = array($tableInfo['Name'], $tableInfo['codePool'], $tableInfo['current'], $tableInfo['latest']);

            $latest = $tableInfo['current'] == $tableInfo['latest'];

            if ($doFormat) {
                $update[] = '<comment>' . ($latest ? 'Yes' : 'No') . '</comment>';
            } else {
                $update[] = ($latest ? 'Yes' : 'No');
            }

            /**
             * No inactive modules are sent back from the Hypernode platform; maybe later?
             */
            /*if($tableInfo['Status'] == 'inactive') {
                $tableInfo['Name'] .= ' <error>(inactive)</error>';
            }*/
            if ($latest) {
                $rowsLatest[] = $update;
            } else {
                $rowsUpdates[] = $update;
            }
        }

        $rows = array_merge($rowsLatest, array(new TableSeparator()), $rowsUpdates);

        $this->getHelper('table')
                ->setHeaders(array('Name', 'Code pool', 'Current version', 'Latest version', 'Newer version available?'))
                ->renderByFormat($output, $rows, $input->getOption('format'));
    }
}
