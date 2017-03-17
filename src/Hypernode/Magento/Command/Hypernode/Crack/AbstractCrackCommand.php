<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Crack;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Hypernode\Magento\PasswordCracker\EngineFactory;
use Hypernode\Util\FileResolver;
use Hypernode\Magento\PasswordCracker\Wordlist\Generator as WordlistGenerator;

/**
 * Class AbstractCrackCommand
 * @package Hypernode\Magento\Command\Hypernode
 */
abstract class AbstractCrackCommand extends AbstractMagentoCommand
{
    protected $input;
    protected $output;
    private $specialPath;

    protected function configure()
    {
        $this
            ->addArgument('wordlists', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Word list files to use as the base passwords.')
            ->addOption('active', null, InputOption::VALUE_NONE, 'Include active users in output')
            ->addOption('inactive', null, InputOption::VALUE_NONE, 'Include inactive users in output')
            ->addOption('cracked', null, InputOption::VALUE_NONE, 'Return rows successfully cracked')
            ->addOption('uncracked', null, InputOption::VALUE_NONE, 'Return rows not cracked')
            ->addOption('engine', null, InputOption::VALUE_REQUIRED, 'Force using specific cracking engine')
            ->addOption('rulesets', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Mutator rulesets to run against wordlists')
            ->addOption('usernames', 'u', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Comma seperated list of usernames to filter by')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Skip details confirmation')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Output format table|csv|json')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->input  = $input;
        $this->output = $output;
        $this->config = $this->getPluginConfig();

        if ($format = $input->getOption('format')) {
            if (!in_array($format, array('csv', 'table', 'json'))) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid format specified [%s].', $format)
                );
            }
        }

        if (extension_loaded('xdebug')) {
            $output->writeln('<warning>The xdebug extension was detected, this will significantly slow down the cracking rate.</warning>');
        }

        foreach ($this->config['wordlistDirs'] as $dir) {
            $path = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'special.txt';
            if (file_exists($path)) {
                $output->writeln(
                    sprintf('<warning>The file %s will never be loaded due to "special" being a reserved list name.</warning>', $path)
                );
                break;
            }
        }
    }

    protected function getInstalledWordlists()
    {
        return $this->getInstalledFiles('wordlistDirs', 'txt');
    }

    protected function getInstalledRules()
    {
        return $this->getInstalledFiles('rulesDirs', 'rule');
    }

    protected function getInstalledFiles($configField, $extension)
    {
        $config = $this->getApplication()
            ->getConfig();

        $files = array();
        foreach ($config['passwordCracker'][$configField] as $dir) {
            foreach (glob(rtrim($dir, '/') . '/*.' . $extension) as $ruleFile) {
                $files[] = basename($ruleFile, '.' . $extension);
            }
        }

        return $files;
    }

    public function getHelp()
    {
        $wordlists = implode(', ', $this->getInstalledWordlists());
        $rules     = implode(', ', $this->getInstalledRules());

        return <<<HTML
 This plugin is intended to check your installation for weak passwords.

 <comment>Filtering:</comment>
 It is possible to filter which passwords to crack by either status or username. Using the <info>--active</info> 
 and <info>--inactive</info> flags allows filtering on the status. Including both flags is the same as including 
 neither. All admin credentials will be checked. The <info>-u</info> flag allows you to specify one or more 
 usernames to be attempted. Both filter types can be used in conjunction with each other and are
 additive. Therefore if you use a username for an inactive user in conjunction with the <info>--active</info> flag, 
 the user will not be checked.
   
 <comment>Engine:</comment>
 This command supports two different engines. Hashcat and PHP. By default it will attempt to determine 
 if shell_exec is enabled and if hashcat is installed on the server, if it finds hashcat it will leverage
 this as the engine as it is many orders of magnitude faster than PHP processing. If hashcat isn't found 
 then it will fall back to pure PHP. Alternatively you can force PHP processing using the <info>--engine=php</info> flag.
 
 The hashcat engine currently only supports cracking CE passwords. Whilst it's slower, one major advantage
 of the PHP engine is that it uses the encryption model configured in Magento. This means if you are using
 EE or a 3rd party replacement due to legacy support or greater security, it can still attempt to brute force
 your passwords. The speed at which it can achieve this will vary based on the hashing mechanism in use.
 
 <comment>Output:</comment>
 It is possible to filter which values get included in the output by using the <info>--cracked</info> and <info>--uncracked</info>
 flags. If either is used independantly then only the passwords that are cracked or uncracked will be
 included in the output. Using both flags is the same as including neither, all row will be output.
 
 When the <info>--json</info> flag is used, the only information output to stdout will be a JSON object
 containing information about the cracked password.
 
 <comment>Rulesets:</comment>
 When specifying a ruleset you can either use file system paths or the name of an installed ruleset.
  
   <info>Installed Rulesets: </info> $rules 
 
 <comment>Wordlists:</comment>
 When specifying word lists you can either use file system paths or the name of an installed word list. The 
 word list <info>special</info> represents an automatically generated word list based on the domain names and information
 about the admin users in the installation.
  
   <info>Installed Wordlists: </info> $wordlists
 
HTML;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output, true);
        if (!$this->initMagento()) {
            return;
        }

        $wordFiles   = $this->getWordFiles();
        $ruleFiles   = $this->getRuleFiles();
        $credentials = $this->getCredentials();

        if (!$this->confirmDetails()) {
            return;
        }

        $start = microtime(true);

        $engine = $this->getEngine($wordFiles, $ruleFiles);
        $results = $engine->crack($credentials);
        $results = $this->filterOutput($results);

        $this->cleanup();

        $data = array(
            'results'    => $results,
            'time_taken' => (int)(microtime(true) - $start),
        );

        if ($input->getOption('format') === 'json') {
            $this->outputJson($data);
        } elseif ($input->getOption('format') === 'csv') {
            $this->outputCsv($data);
        } else {
            $this->outputTable($data);
        }
    }

    protected function getEngine($wordFiles, $ruleFiles)
    {
        $input  = $this->input;
        $output = $this->output
            ->getErrorOutput();

        $factory = new EngineFactory();
        if ($engineType = $input->getOption('engine')) {
            $factory->setEngineType($engineType);
        }
        $factory->setEncryptor(\Mage::helper('core')->getEncryptor());
        $factory->setWordFiles($wordFiles);
        $factory->setRuleFiles($ruleFiles);
        $factory->setOutput($output);

        return $factory->getEngine();
    }

    protected function getPluginConfig()
    {
        $config = $this->getApplication()
            ->getConfig();

        return $config['passwordCracker'];
    }

    protected function confirmDetails()
    {
        if ($this->input->getOption('force')) {
            return true;
        }

        $this->output->writeLn('<comment>Are you sure?</comment>');
        $this->output->writeLn('  This command can be slow.'.PHP_EOL);

        // @todo - confirm details such as number of users, words and rules.
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('  Enter y to proceed: ', false);

        return $helper->ask($this->input, $this->output, $question);
    }

    protected function outputJson($data)
    {
        $output = $this->output;
        $results = array();
        foreach ($data['results'] as $credential) {
            $results[] = array(
                'user'     => $credential->getId(),
                'hash'     => $credential->getHash(),
                'cracked'  => $credential->isCracked() ? true : false,
                'password' => $credential->getPassword(),
            );
        }
        $data['results'] = $results;
        $output->write(json_encode($data));
    }

    protected function outputTable($data)
    {
        $output = $this->output;
        $results = array();
        foreach ($data['results'] as $credential) {
            $results[] = array(
                'user'     => $credential->getId(),
                'hash'     => $credential->getHash(),
                'cracked'  => $credential->isCracked() ? 'Yes' : 'No',
                'password' => $credential->getPassword(),
            );
        }
        $headings = array('User', 'Hash', 'Cracked', 'Password');
        $t = $this->getHelper('table');
        $t->setHeaders($headings)->renderByFormat($output, $results);

        $output->writeln(
            sprintf(
                '<info>Cracking Completed in %s.</info>',
                $this->humanizeTime($data['time_taken'])
            )
        );
    }

    protected function outputCsv($data)
    {
        $output = $this->output;
        $results = array();
        foreach ($data['results'] as $credential) {
            $results[] = array(
                'user'     => $credential->getId(),
                'hash'     => $credential->getHash(),
                'cracked'  => $credential->isCracked() ? '1' : '0',
                'password' => $credential->getPassword(),
            );
        }
        $headings = array('User', 'Hash', 'Cracked', 'Password');
        $t = $this->getHelper('table');
        $t->setHeaders($headings)->renderByFormat($output, $results, 'csv');
    }

    protected function getWordFiles()
    {
        $wordlists = $this->input
            ->getArgument('wordlists');

        $special = array_search('special', $wordlists);
        if ($special !== false) {
            $filePath = $this->generateSpecialWordlist();
            $wordlists = array_replace($wordlists, array($special => $filePath));
        }

        $resolver = new FileResolver($wordlists, $this->config['wordlistDirs'], 'txt');
        $invalid = $resolver->getInvalidFiles();
        if (!empty($invalid)) {
            throw new \InvalidArgumentException(
                sprintf('Wordlist file(s) not found [%s].', implode(', ', $invalid))
            );
        }

        return $resolver->getValidFiles();
    }

    protected function getRuleFiles()
    {
        $usedRuleSets = $this->input->getOption('rulesets');

        $resolver = new FileResolver($usedRuleSets, $this->config['rulesDirs'], 'rule');

        $invalid = $resolver->getInvalidFiles();
        if (!empty($invalid)) {
            throw new \InvalidArgumentException(
                sprintf('Ruleset file(s) not found [%s].', implode(', ', $invalid))
            );
        }

        return $resolver->getValidFiles();
    }

    protected function filterCracked($results, $cracked)
    {
        $filteredResults = array();
        foreach ($results as $result) {
            if ($result->isCracked() === $cracked) {
                $filteredResults[] = $result;
            }
        }

        return $filteredResults;
    }

    protected function filterOutput($results)
    {
        $input     = $this->input;
        $cracked   = $input->getOption('cracked');
        $uncracked = $input->getOption('uncracked');
        if ($cracked && ! $uncracked) {
            return $this->filterCracked($results, true);
        } elseif ($uncracked && ! $cracked) {
            return $this->filterCracked($results, false);
        }

        return $results;
    }

    protected function generateSpecialWordlist()
    {
        $admins = \Mage::getModel('admin/user')->getCollection();
        $stores = \Mage::app()->getStores();
        $generator = new WordlistGenerator();
        $generator->setAdmins($admins);
        $generator->setStores($stores);
        $words = $generator->generate();

        $dir = \Mage::getBaseDir('tmp') . DS . 'password-cracker' . DS;

        $io = new \Varien_Io_File;
        $io->checkAndCreateFolder($dir);

        $id = uniqid('run-', true);
        $this->specialPath = $dir . sprintf('special-words-%s.txt', $id);
        $fh = fopen($this->specialPath, 'w');
        foreach ($words as $word) {
            fwrite($fh, $word . PHP_EOL);
        }

        fclose($fh);

        return $this->specialPath;
    }

    protected function cleanup()
    {
        $path = realpath($this->specialPath);
        if (file_exists($path)
            && strpos($path, \Mage::getBaseDir('tmp')) === 0) {
            unlink($path);
        }
    }

    public function humanizeTime($secs)
    {
        $secs = (int) $secs;
        $units = array(
            'week'   => 7*24*3600,
            'day'    =>   24*3600,
            'hour'   =>      3600,
            'minute' =>        60,
            'second' =>         1,
        );

        // specifically handle zero
        if ($secs === 0) {
            return '0 seconds';
        }
        if ($secs === 1) {
            return '1 second';
        }

        $s = '';
        foreach ($units as $name => $divisor) {
            if ($quot = (int)($secs / $divisor)) {
                $s .= "$quot $name";
                $s .= (abs($quot) > 1 ? 's' : '') . ', ';
                $secs -= $quot * $divisor;
            }
        }

        return substr($s, 0, -2);
    }

    protected function applyStatusFilter($collection)
    {
        $input    = $this->input;
        $active   = $input->getOption('active');
        $inactive = $input->getOption('inactive');
        if ($active || $inactive) {
            $states = array();
            if ($active) {
                $states[] = 1;
            }
            if ($inactive) {
                $states[] = 0;
            }
            $collection->addFieldToFilter('is_active', array('in' => $states));
        }

        return $collection;
    }

    protected function applyUserFilter($collection, $field = 'username')
    {
        $input     = $this->input;
        $usernames = $input->getOption('usernames');
        if (!empty($usernames)) {
            $usernames = array_map('trim', array_filter($usernames));
            $collection->addFieldToFilter($field, array('in' => $usernames));
        }
    }

    abstract protected function getCredentials();
}
