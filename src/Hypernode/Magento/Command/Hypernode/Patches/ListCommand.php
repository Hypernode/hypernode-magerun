<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Patches;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use N98\Util\Console\Helper\Table\Renderer\RendererFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListCommand
 * @package Hypernode\Magento\Command\Hypernode\Patches
 */
class ListCommand extends AbstractHypernodeCommand
{
    const HYPERNODE_PATCH_TOOL_URL = 'https://tools.hypernode.com/patches/';

    private $patchFile;
    private $appliedPatches;

    protected function configure()
    {
        $this
            ->setName('hypernode:patches:list')
            ->setDescription('Determine required patches.')
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
        if (!$this->initMagento()) {
            return;
        }

        $this->patchFile = \Mage::getBaseDir('etc') . DIRECTORY_SEPARATOR . 'applied.patches.list';

        $_isEnterprise = $this->getApplication()->isMagentoEnterprise();

        $_patchUrl = self::HYPERNODE_PATCH_TOOL_URL . ($_isEnterprise ? 'enterprise' : 'community') . DIRECTORY_SEPARATOR . \Mage::getVersion();

        try {
            $curl = $this->getCurl();
            $curl->get($_patchUrl);
            $patchesListJson = $curl->response;
        } catch (Exception $e) {
            $output->writeln('<error>Could not fetch data from Hypernode platform; ' . $e->getMessage() . '</error>');
            exit(1);
        }

        $patchesList = json_decode($patchesListJson, true);
        if (!$patchesList) {
            $output->writeln('<error>Could not fetch patches list from Hypernode platform.</error>');
            exit(1);
        }

        $this->_loadPatchFile();

        if(count($patchesList, COUNT_RECURSIVE) - count($patchesList) <= 0) {
            $output->writeln('<info>No patches are necessary, your installation is up to date!</info>');
            exit();
        }

        $doFormat = $input->getOption('format') === null;

        $rows = array();
        foreach ($patchesList as $patchType => $patches) {
            foreach ($patches as $patch) {

                // Force version postfix by passing through the formatter
                $patchSplit = explode(' ', $patch);
                $patchWithVersion = $this->_formatPatchName(
                    $patchSplit[0],
                    (isset($patchSplit[1]) ? $patchSplit[1] : null)
                );

                // Tell if patch is applied
                $isApplied = isset($this->appliedPatches[$patchWithVersion]);

                if ($isApplied && $doFormat) {
                    $applied = '<info>Yes</info>';
                } else if ($isApplied) {
                    $applied = 'Yes';
                } else if ($doFormat) {
                    $applied = ($patchType == 'required') ? '<error>No</error>' : '<comment>No</comment>';
                } else {
                    $applied = 'No';
                }

                $rows[] = array(
                    $patch,
                    $patchType,
                    $applied
                );
            }
        }

        $this->getHelper('table')
                ->setHeaders(array('Patch', 'Type', 'Applied'))
                ->renderByFormat($output, $rows, $input->getOption('format'));
    }

    /**
     * Use to load the patches array with applied patches.
     *
     * @return void
     *
     * Thanks @philwinkle - https://github.com/philwinkle/Philwinkle_AppliedPatches/
     */
    protected function _loadPatchFile()
    {
        $ioAdapter = new \Varien_Io_File();
        if (!$ioAdapter->fileExists($this->patchFile)) {
            return;
        }
        $ioAdapter->open(array('path' => $ioAdapter->dirname($this->patchFile)));
        $ioAdapter->streamOpen($this->patchFile, 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            if (stristr($buffer, '|')) {
                $patchInfo = array_map('trim', explode('|', $buffer));
                $this->appliedPatches[$this->_formatPatchName($patchInfo[1], $patchInfo[3])] = true;
            }
        }
        $ioAdapter->streamClose();
    }

    /**
     * Format the patch names taken from the patch list file or from the Hypernode API
     *
     * @param string $name Unformatted/inconsistent name
     * @param null|string $version Optional version
     * @return string Formatted name: "SUPEE-1234 v1.2"
     */
    protected function _formatPatchName($name, $version = null)
    {
        if (empty($version)) {
            $version = 'v1';
        }

        /**
         * Remove prefixed "PATCH_"
         * @example "PATCH_SUPEE-9767_CE_1.7.0.2_v1.sh"
         */
        if (substr($name, 0, 5) === 'PATCH') {
            $name = substr($name, 6);
        }

        /**
         * Remove stuff following after SUPEE number starting with an underscore
         * @example "SUPEE-6482_EE_1.13.0.2"
         */
        if (false !== strpos($name, '_')) {
            $name = substr($name, 0, strpos($name, '_'));
        }

        if (false === strpos($name, '-')) {
            // No - anymore at this point, so the $name given is incorrect. Return whatever we have
            return $name . ' ' . $version;
        }

        /**
         * Remove stuff following after SUPEE number starting with a dash (the second dash)
         * @example "SUPEE-7405-CE-1-7-0-2"
         */
        if (false !== strpos($name, '-', strpos($name, '-') + 1)) {
            $name = substr($name, 0, strpos($name, '-', strpos($name, '-') + 1));
        }

        return $name . ' ' . $version;
    }
}

