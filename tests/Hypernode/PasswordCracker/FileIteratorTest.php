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
class FileIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyRowsSkipped()
    {
        $root = vfsStream::setup('home');
        vfsStream::newFile('test.txt')
            ->at($root)
            ->setContent("abc\n\n\n\ndef");

        $iterator = new FileIterator(vfsStream::url('home/test.txt'));

        $this->assertEquals(2, iterator_count($iterator));
    }

    public function testRowsAreTrimmed()
    {
        $root = vfsStream::setup('home');
        vfsStream::newFile('test.txt')
            ->at($root)
            ->setContent(" abc\ndef ");

        $iterator = new FileIterator(vfsStream::url('home/test.txt'));

        $expected = array('abc', 'def');
        $this->assertEquals($expected, iterator_to_array($iterator));
    }

    public function testExceptionIfCantRead()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $iterator = new FileIterator('/flibble');
    }
}
