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

use Hypernode\PasswordCracker\Cracker;
use Hypernode\PasswordCracker\FilesIterator;
use Hypernode\PasswordCracker\MutatedWordIterator;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Hypernode\PasswordCracker\RuleIterator;
use Hypernode\PasswordCracker\RuleFactory;

class PHP extends AbstractEngine
{
    private $encryptor;
    private $words;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (! isset($options['encryptor'])) {
            throw new InvalidArgumentException(
                'Missing required constructor option "encryptor"'
            );
        }
        $this->encryptor = $options['encryptor'];
    }

    public function crack($credentials)
    {
        $count = count($credentials);

        $cracker = new Cracker();
        $cracker->setWords($this->getWords());
        $cracker->setEncryptor($this->encryptor);

        foreach ($credentials as $k => $credential) {
            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->output->writeLn(
                    sprintf(
                        '[%s/%s] Cracking %s',
                        $k + 1,
                        $count,
                        $credential->getId()
                    )
                );
            } else {
                $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
            }

            $cracker->crack($credential);


            $this->output->writeLn('');
        }

        $this->output->writeLn('Complete!'.PHP_EOL);

        return $credentials;
    }

    /**
     * @return \Iterator
     */
    protected function getWords()
    {
        if (null === $this->words) {
            $rules = RuleFactory::createFromDefinitionSet(
                new RuleIterator(
                    new FilesIterator($this->getRuleFiles())
                )
            );
            $this->words = new MutatedWordIterator(
                new FilesIterator($this->getWordFiles()),
                $rules
            );
            $this->words->setProgress(new ProgressBar($this->output, $this->words->count()));
        }

        return $this->words;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }
}
