<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Varnish;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class VarnishFlushCommand
 * @package Hypernode\Magento\Command\Hypernode\Varnish
 */
class VarnishFlushCommand extends AbstractHypernodeCommand
{


    protected function configure()
    {
        $this
            ->setName('hypernode:varnish:flush')
            ->setDescription('Flushes all cached varnish URL\'s.');
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

                $flush = $admin->flushAll();

                if($flush){
                    $output->writeln("<info>Flushed cached varnish URL's</info>");
                }

            } else {
                $output->writeln('<error>Turpentine is not enabled or installed.</error>');
            }
        }
    }
}

?>
