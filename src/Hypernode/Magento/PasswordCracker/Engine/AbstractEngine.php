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

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractEngine
{
    protected $ruleFiles     = array();
    protected $wordlistFiles = array();
    protected $output;
    protected $encryptionClass;

    public function __construct(array $options)
    {
        $this->encryptionClass = isset($options['encryptor']) ?
            get_class($options['encryptor']) :
            'Mage_Core_Model_Encryption';
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param array $ruleFiles
     */
    public function setRuleFiles(array $ruleFiles)
    {
        $this->ruleFiles = $ruleFiles;
    }

    /**
     * @return array;
     */
    public function getRuleFiles()
    {
        return $this->ruleFiles;
    }

    /**
     * @param array $wordFiles
     */
    public function setWordFiles(array $wordFiles)
    {
        $this->wordFiles = $wordFiles;
    }

    /**
     * @return array
     */
    public function getWordFiles()
    {
        return $this->wordFiles;
    }

    abstract public function crack($hash);
    abstract public function isAvailable();
}
