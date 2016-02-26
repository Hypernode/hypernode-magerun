<?php

namespace Hypernode\Magento\Command;

use Hypernode\Curl;
use N98\Magento\Command\AbstractMagentoCommand;

abstract class AbstractHypernodeCommand extends AbstractMagentoCommand
{

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * Get curl class
     *
     * @return Curl
     */
    public function getCurl()
    {
        if (null === $this->_curl) {
            $this->_curl = new Curl();
        }

        return $this->_curl;
    }

    /**
     * Set curl
     *
     * @param Curl $curl
     * @return $this
     */
    public function setCurl(Curl $curl)
    {
        $this->_curl = $curl;

        return $this;
    }
}


