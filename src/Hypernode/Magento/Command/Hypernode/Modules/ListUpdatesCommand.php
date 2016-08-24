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

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use N98\Util\Console\Helper\Table\Renderer\RendererFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableSeparator;
use N98\Magento\Modules;

/**
 * Class ListUpdatesCommand
 * @package Hypernode\Magento\Command\Hypernode\Modules
 */
class ListUpdatesCommand extends AbstractHypernodeCommand
{
    const TOOLS_HYPERNODE_MODULE_URL = 'https://tools.hypernode.com/modules/magerun.json';

    protected function configure()
    {
        $this
            ->setName('hypernode:modules:list-updates')
            ->addOption('codepool', null, InputOption::VALUE_OPTIONAL, 'Show modules in a specific codepool')
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'Show modules with a specific status')
            ->addOption('vendor', null, InputOption::VALUE_OPTIONAL, 'Show modules of a specified vendor')
            ->setDescription('Find available updates for installed modules.')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_OPTIONAL,
                'Output Format. One of [' . implode(',', RendererFactory::getFormats()) . ']'
            );
    }

    /**
     * Get modules
     *
     * @return Modules
     */
    public function getModules()
    {
        $modules = new Modules();
        return $modules->findInstalledModules();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return;
        }

        $modules = $this->getModules()
                ->filterModules($input);

        if (!count($modules)) {
            $output->writeln('<error>Could not parse sys:modules:list</error>');
            exit;
        }

        // Compatibility fix
        $listModules = array_map(function($item){
            return array_combine(array('codePool', 'Name', 'Version', 'Status'), $item);
        }, iterator_to_array($modules));

        $options = JSON_FORCE_OBJECT;
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $options |= JSON_PRETTY_PRINT;
        }
        $listModulesJson = json_encode($listModules, $options);

        $modulesInfo = array();
        foreach ($listModules as $moduleInfo) {
            $modulesInfo[$moduleInfo['Name']] = $moduleInfo;
        }

        try {

            $curl = $this->getCurl();

            $curl->setHeader('Accept', 'application/json');
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Content-Length', strlen($listModulesJson));

            $curl->post(self::TOOLS_HYPERNODE_MODULE_URL, $listModulesJson);

            $response = $curl->response;
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

            $hasUpdate = $tableInfo['current'] != $tableInfo['latest'];

            if ($doFormat) {
                $update[] = ($hasUpdate ? '<comment>Yes</comment>' : '<info>No</info>') . '';
            } else {
                $update[] = ($hasUpdate ? 'Yes' : 'No');
            }

            /**
             * No inactive modules are sent back from the Hypernode platform; maybe later?
             */
            /*if($tableInfo['Status'] == 'inactive') {
                $tableInfo['Name'] .= ' <error>(inactive)</error>';
            }*/
            if ($hasUpdate) {
                $rowsUpdates[] = $update;
            } else {
                $rowsLatest[] = $update;
            }
        }

        $rows = array_merge($rowsLatest, array(new TableSeparator()), $rowsUpdates);

        $this->getHelper('table')
                ->setHeaders(array('Name', 'Code pool', 'Current version', 'Latest version', 'Newer version available?'))
                ->renderByFormat($output, $rows, $input->getOption('format'));
    }

}

