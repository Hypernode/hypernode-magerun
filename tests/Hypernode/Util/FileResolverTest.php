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

use org\bovigo\vfs\vfsStream;

/**
 * Class FileResolverTest
 * @package Hypernode\PasswordCracker
 */
class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        vfsStream::setup('root', '666', array(
            'a' => array(
                'foo.txt' => 'foo',
            ),
            'b' => array(
                'bar.txt' => 'bar',
                'bar.rule' => 'bar',
            ),
            'c' => array(
                'baz.rule' => 'baz',
            )
        ));
    }

    public function testFileNames()
    {
        $resolver = new FileResolver(
            array('foo', 'bar', 'missing'),
            array(vfsStream::url('root/a'), vfsStream::url('root/b')),
            'txt'
        );

        $this->assertEquals(
            array(
                'vfs://root/a/foo.txt',
                'vfs://root/b/bar.txt'
            ),
            $resolver->getValidFiles()
        );

        $this->assertEquals(
            array('missing'),
            $resolver->getInvalidFiles()
        );
    }

    public function testValidFullPath()
    {
        $resolver = new FileResolver(
            array('vfs://root/c/baz.rule'),
            array(vfsStream::url('root/a'), vfsStream::url('root/b')),
            'rule'
        );

        $this->assertEquals(
            array('vfs://root/c/baz.rule'),
            $resolver->getValidFiles()
        );
    }
}
