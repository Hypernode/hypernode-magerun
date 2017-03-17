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
 * Class AdminPasswordCommand
 * @package Hypernode\Magento\Command\Hypernode\Crack
 */
class AdminPasswordCommand extends AbstractCrackCommand
{
    protected function configure()
    {
        $this
            ->setName('hypernode:crack:admin-passwords')
            ->setDescription('Attempt to crack admin credentials');

        parent::configure();
    }

    /**
     * @return array
     */
    protected function getCredentials()
    {
        $admins = $this->getAdmins();
        $credentials = array();
        foreach ($admins as $admin) {
            $credentials[] = new Credential($admin->getPassword(), $admin->getUsername());
        }

        return $credentials;
    }

    /**
     * @return \Mage_Admin_Model_Resource_User_Collection
     */
    protected function getAdmins()
    {
        $admins = \Mage::getModel('admin/user')->getCollection();
        $this->applyUserFilter($admins);
        $this->applyStatusFilter($admins);

        return $admins;
    }
}
