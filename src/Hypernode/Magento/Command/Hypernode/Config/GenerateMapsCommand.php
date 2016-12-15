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

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

/**
 * Class GenerateMapsCommand
 * @package Hypernode\Magento\Command\Hypernode\Config
 */
class GenerateMapsCommand extends AbstractHypernodeCommand
{

    protected function configure()
    {
        $this
            ->setName('hypernode:maps-generate')
            ->setDescription('Generates magerun maps for nginx by store config')
            ->addOption('save', 's', InputOption::VALUE_NONE, 'Save the maps to file [http.magerunmaps]');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return;
        }

        $this->writeSection($output, 'Mage run maps for Nginx. [Byte Hypernode]');

        $defaultStoreFront = \Mage::app()->getDefaultStoreView()->getCode();

        $map = 'map $host $storecode {';
        $map .= "\n hostnames; \n";
        $map .= "default " . $defaultStoreFront . ";\n";

        foreach (\Mage::app()->getStores() as $store) {
            $map .= '.' . str_replace('www.', '', parse_url(\Mage::getStoreConfig('web/unsecure/base_url', $store))['host']) . " " . $store->getCode() . "; \n";
        }

        $map .= "}";

        $output->writeln($map); // output it always

        if ($input->getOption('save')) {

            $helper = $this->getHelper('question');
            $question = new Question('<question>File name / location (relative from Magento): [default: http.magerunmaps]: </question>', 'http.magerunmaps');
            $filename = $helper->ask($input, $output, $question);

            if (($filename = $this->_magentoRootFolder . DS . $filename) && !file_exists($filename)) {
                if (file_put_contents($filename, $map)) {
                    $output->writeln('<info>Successfully wrote Nginx maps to <comment>' . $filename . '</comment></info>');
                } else {
                    $output->writeln("<error>There was an issue writing to " . $filename . ".</error>");
                }
            } else {
                $output->writeln('<info>File already exists.</info>');
            }
        }
    }
}

