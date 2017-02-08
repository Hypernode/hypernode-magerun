<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\Command\Hypernode\Crack;

use Hypernode\PasswordCracker\Credential;

/**
 * Class ApiKeyCommand
 * @package Hypernode\Magento\Command\Hypernode\Crack
 */
class ApiKeyCommand extends AbstractCrackCommand
{
    protected function configure()
    {
        $this
            ->setName('hypernode:crack:api-keys')
            ->setDescription('Attempt to crack api keys for SOAP / XML-RPC users');

        parent::configure();
    }

    /**
     * @return array
     */
    protected function getCredentials()
    {
        $users = $this->getApiUsers();
        $credentials = array();
        foreach ($users as $user) {
            $credentials[] = new Credential($user->getApiKey(), $user->getUsername());
        }

        return $credentials;
    }

    /**
     * @return \Mage_Api_Model_Resource_User_Collection
     */
    protected function getApiUsers()
    {
        $users = \Mage::getModel('api/user')->getCollection();
        $this->applyUserFilter($users);
        $this->applyStatusFilter($users);

        return $users;
    }
}
