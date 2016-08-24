<?php

namespace Hypernode\Magento\Command\Hypernode\Varnish;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigSaveCommand extends AbstractHypernodeCommand
{

    protected function configure()
    {
        $this
            ->setName('hypernode:varnish:config-save')
            ->setDescription('Save and apply Turpentine\'s VCL configuration to Varnish');
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
            if (\Mage::helper('core')->isModuleEnabled('Nexcessnet_Turpentine')) {
                $admin = \Mage::getModel('turpentine/varnish_admin');
                $cfgr = $admin->getConfigurator();

                if (!$cfgr) {
                    throw new \RuntimeException('Could not connect to Varnish admin port. Please check settings (port, secret key).');
                }

                $result = $cfgr->save($cfgr->generate());

                # (success, optional_error)
                if (!$result[0]) {
                    throw new \RuntimeException('Could not save VCL to disk: ' . $result[1]);
                }

                $result = $admin->applyConfig();

                if (!(bool)array_product($result)) {
                    throw new \RuntimeException('Could not apply VCL to all running Varnish instances');
                } else {
                    $output->writeln('<info>The Varnish VCL has successfully been generated and loaded.</info>');
                }
            } else {
                $output->writeln('<error>Turpentine is not enabled or installed.</error>');
            }
        }
    }
}

?>
