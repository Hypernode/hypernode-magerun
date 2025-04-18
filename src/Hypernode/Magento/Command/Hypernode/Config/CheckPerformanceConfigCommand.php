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
use Symfony\Component\Console\Helper\Table;

/**
 * Class GenerateMapsCommand
 * @package Hypernode\Magento\Command\Hypernode\Config
 */
class CheckPerformanceConfigCommand extends AbstractHypernodeCommand
{
    protected function configure()
    {
        $this
            ->setName('hypernode:check-performance-config')
            ->setDescription('Creates a little report if the magento configuration is optimal based on Redis, Varnish, and more');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return 1;
        }

        $output->writeln('<info>Checking Magento Performance Configuration...</info>');

        // Fetch configuration values
        $config = $this->getMagentoConfig();

        $checks = [
            'cache.frontend.page_cache' => [
                'current' => $config['cache']['cache.frontend.page_cache'] ?? 'Unknown',
                'optimal' => 'Magento\Framework\Cache\Backend\Redis',
            ],
            'session.save' => [
                'current' => $config['cache']['session.save'] ?? 'Unknown',
                'optimal' => 'redis',
            ],
            'cache.frontend.default.backend' => [
                'current' => $config['cache']['cache.frontend.default.backend'] ?? 'Unknown',
                'optimal' => 'Magento\Framework\Cache\Backend\Redis',
            ],
            'http_cache_hosts' => [
                'current' => $config['cache']['http_cache_hosts'] ?? 'Unknown',
                'optimal' => 'varnish:6081',
            ],
            'deploy:mode' => [
                'current' => $config['deploy_mode'] ?? 'Unknown',
                'optimal' => 'production',
            ],
        ];

        // Create a table to display results
        $table = new Table($output);
        $table->setHeaders(['Configuration', 'Current Value', 'Optimal Value']);

        foreach ($checks as $key => $check) {
            $current = $check['current'];
            $optimal = $check['optimal'];
            $color = $current === $optimal ? 'green' : 'red';

            $table->addRow([
                $key,
                "<fg=$color>$current</>",
                $optimal,
            ]);
        }

        $table->render();

        $output->writeln('<info>Performance configuration check completed.</info>');
        return 0;
    }

    private function getMagentoConfig(): array
    {
        // Load the env.php file
        $envFilePath = BP . '/app/etc/env.php';
        if (!file_exists($envFilePath)) {
            throw new \RuntimeException('env.php file not found.');
        }

        $envConfig = include $envFilePath;

        // Fetch deployment mode
        $deployMode = \Magento\Framework\App\State::MODE_DEFAULT;
        if (isset($envConfig['MAGE_MODE'])) {
            $deployMode = $envConfig['MAGE_MODE'];
        }

        // Fetch cache configurations
        $cacheConfig = [
            'cache.frontend.page_cache' => $envConfig['cache']['frontend']['page_cache']['backend'] ?? 'Unknown',
            'session.save' => $envConfig['session']['save'] ?? 'Unknown',
            'cache.frontend.default.backend' => $envConfig['cache']['frontend']['default']['backend'] ?? 'Unknown',
            'http_cache_hosts' => isset($envConfig['http_cache_hosts'][0]) 
                ? ($envConfig['http_cache_hosts'][0]['host'] . ':' . $envConfig['http_cache_hosts'][0]['port']) 
                : 'Unknown',];

        return [
            'cache' => $cacheConfig,
            'deploy_mode' => $deployMode,
        ];
    }
}

