<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Util;

/**
 * Class FileResolver
 * @package Hypernode\PasswordCracker
 */
class FileResolver
{
    /**
     * @var array
     */
    private $validFiles = array();

    /**
     * @var array
     */
    private $invalidFiles = array();

    /**
     * FileResolver constructor.
     * @param array $files
     * @param array $directories
     * @param $extension
     */
    public function __construct(array $files, array $directories, $extension)
    {
        $validFiles   = array();
        $invalidFiles = array();

        foreach ($files as $k => $file) {
            foreach ($directories as $dir) {
                $filePath = sprintf('%s/%s.%s', rtrim($dir, '/'), $file, $extension);
                if (is_readable($filePath)) {
                    $validFiles[] = $filePath;
                    continue 2;
                }
            }
            $invalidFiles[] = $file;
        }


        foreach ($invalidFiles as $k => $file) {
            if (is_file($file) && is_readable($file)) {
                $validFiles[] = $file;
                unset($invalidFiles[$k]);
            }
        }

        $this->validFiles = $validFiles;
        $this->invalidFiles = $invalidFiles;
    }

    /**
     * @return array
     */
    public function getValidFiles()
    {
        return $this->validFiles;
    }

    /**
     * @return array
     */
    public function getInvalidFiles()
    {
        return $this->invalidFiles;
    }
}
