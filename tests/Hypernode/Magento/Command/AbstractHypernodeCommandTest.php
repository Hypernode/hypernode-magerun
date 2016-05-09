<?php

namespace Hypernode\Magento\Command;

use Hypernode\Curl;

class AbstractHypernodeCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractHypernodeCommand
     */
    protected $_abstract;

    public function setUp()
    {
        $abstractClass = $this->getMockForAbstractClass(AbstractHypernodeCommand::class, ['testAbstract']);
        $this->_abstract = $abstractClass;
    }

    public function testSetter()
    {
        $abstract = $this->_abstract;
        $mock = $this->getMock(Curl::class, []);

        $return = $abstract->setCurl($mock);
        $this->assertEquals($abstract, $return);
        $this->assertEquals($mock, $abstract->getCurl());
    }

    public function testGetter()
    {
        $abstract = $this->_abstract;

        $origCurl = $abstract->getCurl();
        $this->assertInstanceOf(Curl::class, $origCurl);

        $abstract->setCurl(new Curl());
        $this->assertNotEquals($origCurl, $abstract->getCurl());
    }

}
