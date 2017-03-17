<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\PasswordCracker\Engine;

use Symfony\Component\Process\Process;

class Hashcat extends AbstractEngine
{
    private $dir;
    private $knownEncryptions = array(
        20   => 'Mage_Core_Model_Encryption',
        1420 => 'Enterprise_Pci_Model_Encryption'
    );

    public function getFilepath($file)
    {
        return $this->dir . sprintf('%s-%s.txt', $file, $this->id);
    }

    public function crack($credentials)
    {
        // @todo - inject dependency
        $this->dir = \Mage::getBaseDir('tmp') . DS . 'password-cracker' . DS;
        $this->id  = uniqid('run-', true);

        // @todo - remove / inject dependency
        $io = new \Varien_Io_File;
        $io->checkAndCreateFolder($this->dir);

        $hashesPath  = $this->getFilepath('hashes');
        $potfilePath = $this->getFilepath('potfile');
        $rulesPath   = $this->getFilepath('rules');

        $this->generateHashFile($credentials, $hashesPath);

        $command = $this->generateCommand($rulesPath, $hashesPath, $potfilePath);

        $this->output->writeLn($command);

        // @todo - inject dependency
        $process = new Process($command);
        $process->run();

        $hashMap = $this->parsePotFile($potfilePath);
        foreach ($credentials as $credential) {
            $hash = $credential->getHash();
            if (isset($hashMap[$hash])) {
                $credential->setPassword($hashMap[$hash]);
            }
        }

        unlink($hashesPath);
        unlink($rulesPath);
        unlink($potfilePath);

        return $credentials;
    }

    protected function generateHashFile($credentials, $hashesPath)
    {
        foreach ($credentials as $credential) {
            file_put_contents($hashesPath, $credential->getHash() . PHP_EOL, FILE_APPEND);
        }
    }

    protected function generateCommand($rulesPath, $hashesPath, $potfilePath)
    {
        $command = array(
            'hashcat',
            '--quiet',
            '-m ' .escapeshellarg(array_search($this->encryptionClass, $this->knownEncryptions, true)),
        );

        /*
         * when using multiple -r args, hashcat permutes all rules against each other,
         * this leads to memory issues with even pretty small files. Instead  we generate
         * a single input file with all rules being applied as intended.
         */
        foreach ($this->getRuleFiles() as $file) {
            $contents = file_get_contents($file);
            file_put_contents($rulesPath, $contents, FILE_APPEND);
        }
        $command[] = '-r ' . escapeshellarg($rulesPath);
        $command[] = '--potfile-path ' . escapeshellarg($potfilePath);
        $command[] = escapeshellarg($hashesPath);

        foreach ($this->getWordFiles() as $file) {
            $command[] = escapeshellarg($file);
        }

        return implode(' ', $command);
    }

    /**
     * @param $potfilePath
     * @return array
     */
    protected function parsePotFile($potfilePath)
    {
        $cracked = file($potfilePath);
        $hashMap = array();
        foreach ($cracked as $row) {
            if (preg_match('~([^:].*:[^:]*):(.*)~', $row, $matches)) {
                $hashMap[$matches[1]] = $matches[2];
            }
        }

        return $hashMap;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->canHashcat() && $this->isKnownEncryption();
    }

    /**
     * @return bool
     */
    protected function canHashcat()
    {
        if (!$this->canShellExec()) {
            return false;
        }

        return empty(shell_exec('which hashcat 2>/dev/null')) ? false : true;
    }

    /**
     * @return bool
     */
    protected function canShellExec()
    {
        return is_callable('shell_exec')
            && false === stripos(ini_get('disable_functions'), 'shell_exec');
    }

    /**
     * @return bool
     */
    protected function isKnownEncryption()
    {
        return in_array($this->encryptionClass, $this->knownEncryptions, true);
    }
}
