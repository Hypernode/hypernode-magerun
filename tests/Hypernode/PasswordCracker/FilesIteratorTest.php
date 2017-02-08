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

use org\bovigo\vfs\vfsStream;

/**
 * Class FileIteratorTest
 * @package Hypernode\PasswordCracker
 */
class FilesIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testMultipleFilesIterated()
    {
        $fs = vfsStream::setup('root', '666', array(
            'foobar.txt' => "abc\ndef",
            'baz.txt'    => "123",
        ));

        $iterator = new FilesIterator(array(
            vfsStream::url('root/foobar.txt'),
            vfsStream::url('root/baz.txt')
        ));

        $this->assertEquals(3, iterator_count($iterator));
    }
}
