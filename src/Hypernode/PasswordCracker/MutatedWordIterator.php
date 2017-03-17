<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\PasswordCracker;

use Symfony\Component\Console\Helper\ProgressBar;

class MutatedWordIterator implements \Iterator
{
    /** @var \Iterator */
    protected $words;
    /** @var \Iterator */
    protected $rules;
    /** @var int */
    protected $key = 0;
    /** @var  ProgressBar */
    protected $progress;

    /**
     * MutatedWordIterator constructor.
     * @param \Iterator $words
     * @param \Iterator $rules
     */
    public function __construct(\Iterator $words, \Iterator $rules)
    {
        $this->words = $words;
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function current()
    {
        $word = $this->words->current();
        $rule = $this->rules->current();

        return $rule->process($word);
    }

    public function next()
    {
        $this->rules->next();
        $this->key++;
        if ($this->progress) {
            $this->progress->advance();
        }
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        if (! $this->rules->valid()) {
            $this->rules->rewind();
            $this->words->next();
        }

        return $this->words->valid();
    }

    public function rewind()
    {
        $this->key = 0;
        $this->words->rewind();
        $this->rules->rewind();
        if ($this->progress) {
            $this->progress->start();
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return \iterator_count($this->words) * \iterator_count($this->rules);
    }

    /**
     * @param ProgressBar $progress
     */
    public function setProgress(ProgressBar $progress)
    {
        $this->progress = $progress;
        $progress->setRedrawFrequency(\iterator_count($this->rules));
        $progress->setFormat('  %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
    }
}
