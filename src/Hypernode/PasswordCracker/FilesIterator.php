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
 * Class FilesIterator
 * @package Hypernode\PasswordCracker
 */
class FilesIterator extends \AppendIterator
{
    /**
     * FilesIterator constructor.
     * @param array $files
     */
    public function __construct(array $files)
    {
        parent::__construct();

        foreach ($files as $file) {
            $this->append(new FileIterator($file));
        }
    }
}
