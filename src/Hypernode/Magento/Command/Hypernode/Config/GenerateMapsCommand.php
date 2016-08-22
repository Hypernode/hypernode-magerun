<?php

namespace Hypernode\Magento\Command\Hypernode\Config;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

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

        $helper = $this->getHelper('question');
        $question = new Question('<question>Save the maps to [default: http.magerunmaps]: </question>', 'http.magerunmaps');

        $defaultStoreFront = \Mage::app()->getDefaultStoreView()->getCode();
        $map = 'map $host $storecode {';
        $map .= "\n hostnames; \n";
        $map .= "default " . $defaultStoreFront . ";\n";

        foreach (\Mage::app()->getStores() as $store) {
            $map .= "." . parse_url(\Mage::getStoreConfig('web/unsecure/base_url', $store))['host'] . " " . $store->getCode() . "; \n";
            $table[$store->getId()] = array(
                $store->getId(),
                $store->getCode(),
                \Mage::getStoreConfig('web/unsecure/base_url', $store),
                \Mage::getStoreConfig('web/secure/base_url', $store),
            );
        }

        $map .= "}";

        if ($input->getOption('save')) {
            $filename = $helper->ask($input, $output, $question);
            if ($filename && !file_exists($filename)) {
                if (file_put_contents($filename, $map)) {
                    $output->writeln('<info>Succesfully written maps to ' . $filename . '</info>');
                } else {
                    $output->writeln("<error>There was an issue writing to " . $filename . ".</error>");
                }
            } else {
                $output->writeln('<info>File already exists, outputting it.</info>');
                $this->writeSection($output, 'Mage run maps for Nginx. [Byte Hypernode]');
                $output->writeln($map);
            }
        } else {
            $this->writeSection($output, 'Mage run maps for Nginx. [Byte Hypernode]');
            $output->writeln($map);
        }

    }

}