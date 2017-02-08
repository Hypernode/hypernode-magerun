<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\PasswordCracker;

class EngineFactory
{
    private $engine;
    private $engineType;
    private $encryptor;

    public function setEngineType($type)
    {
        $this->engineType = $type;
    }

    public function getEngineType()
    {
        return $this->engineType;
    }

    public function setOutput($output)
    {
        $this->getEngine()->setOutput($output);
    }

    public function setRuleFiles($ruleFiles)
    {
        $this->getEngine()->setRuleFiles($ruleFiles);
    }

    public function setWordFiles($wordFiles)
    {
        $this->getEngine()->setWordFiles($wordFiles);
    }

    public function setEncryptor($encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @return Engine\AbstractEngine
     */
    public function getEngine()
    {
        if (null === $this->engine) {
            $this->engine = $this->determineEngine();
        }

        return $this->engine;
    }

    protected function getEngines()
    {
        return array(
            'hashcat' => new Engine\Hashcat(
                array(
                    'encryptor' => $this->encryptor,
                )
            ),
            'php'     => new Engine\PHP(
                array(
                    'encryptor' => $this->encryptor,
                )
            ),
        );
    }

    /**
     * @return Engine\AbstractEngine
     */
    protected function determineEngine()
    {
        $engines = $this->getEngines();
        if ($this->engineType && isset($engines[$this->engineType])) {
            $engine = $engines[$this->engineType];
            if ($engine->isAvailable()) {
                return $engine;
            }

            throw new \InvalidArgumentException(
                sprintf('The engine %s is not available', $this->engineType)
            );
        }

        foreach ($this->getEngines() as $engine) {
            if ($engine->isAvailable()) {
                return $engine;
            }
        }
    }
}
