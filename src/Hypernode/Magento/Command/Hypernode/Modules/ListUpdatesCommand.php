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
            ->addOption('only-active', null, InputOption::VALUE_NONE, 'Ignore inactive modules')
            ->setDescription('Find available updates for installed modules.')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_OPTIONAL,
                'Output Format. One of [' . implode(',', RendererFactory::getFormats()) . ']'
            );
    }

    /**
     * Returns an Object with Magento modules
     * @param $input InputInterface
     * @return Modules
     */
    public function getModules($input)
    {
        $modules = new Modules();
        $modules = $modules->findInstalledModules()->filterModules($input);

        return $modules;
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

        $onlyActive = $input->getOption('only-active') ? true : false;
        $modules = $this->getModules($input);

        if (!count($modules)) {
            $output->writeln('<error>Could not parse sys:modules:list</error>');
            exit;
        }

        // Compatibility fix
        $listModules = array_map(
            function ($item) {
                return array_combine(array('codePool', 'Name', 'Version', 'Status'), $item);
            },
            iterator_to_array($modules)
        );

        $options = JSON_FORCE_OBJECT;
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $options |= JSON_PRETTY_PRINT;
        }

        $modulesInfo = array();
        foreach ($listModules as $moduleInfo) {

            if (!$moduleInfo['Version']) {
                $moduleInfo['Version'] = $this->getExtensionVersion($moduleInfo['Name']);
            }
            $modulesInfo[$moduleInfo['Name']] = $moduleInfo;
        }

        $modulesInfoJson = json_encode($modulesInfo, $options);

        try {

            $curl = $this->getCurl();

            $curl->setHeader('Accept', 'application/json');
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Content-Length', strlen($modulesInfoJson));

            $curl->post(self::TOOLS_HYPERNODE_MODULE_URL, $modulesInfoJson);

            $response = $curl->response;


        } catch (\Exception $e) {
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

            // inactive / active filtering
            if ($tableInfo['Status'] === 'inactive' && $doFormat && !$onlyActive) {
                $tableInfo['Name'] = '<error>' . $tableInfo['Name'] . ' (inactive)</error>';
            } elseif ($onlyActive && $tableInfo['Status'] === 'inactive') {
                continue;
            }

            $update = array(
                $tableInfo['Name'],
                $tableInfo['codePool'],
                $tableInfo['current'],
                $tableInfo['latest'],
                $tableInfo['Status'],
            );

            $hasUpdate = $tableInfo['current'] !== $tableInfo['latest'];

            if ($doFormat) {
                $update[] = ($hasUpdate ? '<comment>Yes</comment>' : '<info>No</info>') . '';
            } else {
                $update[] = ($hasUpdate ? 'Yes' : 'No');
            }

            if ($hasUpdate) {
                $rowsUpdates[] = $update;
            } else {
                $rowsLatest[] = $update;
            }
        }

        // do not show empty row if there are no updates
        if (count($rowsUpdates) < 1) {
            $rows = $rowsLatest;
        } else {
            $rows = array_merge($rowsLatest, array(new TableSeparator()), $rowsUpdates);
        }

        $this->getHelper('table')
            ->setHeaders(
                array('Name', 'Code pool', 'Current version', 'Latest version', 'Status', 'Newer version available?')
            )
            ->renderByFormat($output, $rows, $input->getOption('format'));
    }

    /**
     * Finds out the version of a disabled / inactive module
     *
     * 1. Checks if the module version can be found in config (cache)
     * 2. Else checks the modules' config.xml for the version
     *
     * @param $namespace
     * @return bool|string|null
     */
    protected function getExtensionVersion($namespace)
    {
        /**
         * Is it in config cache?
         */
        if ($versionFromCache = (string)\Mage::getConfig()->getNode()->modules->{$namespace}->version) {
            return $versionFromCache;
        }

        $moduleDir = \Mage::getConfig()->getModuleDir('etc', $namespace);

        if (file_exists($moduleDir)) {

            $configFile = $moduleDir . DIRECTORY_SEPARATOR . 'config.xml';

            if ($xml = file_get_contents($configFile)) {

                $config = new \Varien_Simplexml_Config($xml);

                if ($version = $config->getNode('modules')->descend($namespace)->asArray()) {
                    return $version['version'];
                }
            }
        }

        // returning null for version will exclude the module from getting version info
        return null;
    }

}

