<?php

namespace Hypernode\Magento\Command\System\Patches;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use N98\Util\Console\Helper\Table\Renderer\RendererFactory;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractHypernodeCommand
{
    const HYPERNODE_PATCH_TOOL_URL = 'https://tools.hypernode.com/patches/';

    private $patchFile;
    private $appliedPatches;

    protected function configure()
    {
        $this
            ->setName('sys:patches:list')
            ->setAliases(['sys:info:patches']) // Backwards compatible
            ->setDescription('Determine required patches [Hypernode]')
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
            $curl->setopt(CURLOPT_SSL_VERIFYPEER, 0);
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

                // Tell if patch is applied
                $isApplied = isset($this->appliedPatches[$patch]);

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
                list($date, $patch) = array_map('trim', explode('|', $buffer));
                $this->appliedPatches[$patch] = true;
            }
        }
        $ioAdapter->streamClose();
    }

}
