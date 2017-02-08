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

/**
 * Class FileIterator
 * @package Hypernode\PasswordCracker
 */
class FileIterator extends \SplFileObject
{
    /**
     * FileIterator constructor.
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(
                sprintf('The file %s isn\'t readable.', $fileName)
            );
        }

        parent::__construct($fileName, 'r');

        $this->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
    }

    /**
     * @return string
     */
    public function current()
    {
        return trim(parent::current());
    }
}
