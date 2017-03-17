<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\Magento\PasswordCracker\Wordlist;

class Generator
{
    protected $admins;
    protected $stores;

    public function setStores($stores)
    {
        $this->stores = $stores;
    }

    public function setAdmins($admins)
    {
        $this->admins = $admins;
    }

    public function generate()
    {
        $words = array();
        foreach ($this->admins as $admin) {
            $username      = $admin->getUsername();
            $firstName     = $admin->getFirstname();
            $lastName      = $admin->getLastname();
            $email         = $admin->getEmail();
            $emailPrefix   = substr($email, 0, strpos($email, '@'));
            $emailDomain   = substr($email, strpos($email, '@')+1);
            $primaryDomain = substr($emailDomain, 0, strpos($emailDomain, '.'));
            $words[]       = $username;
            $words[]       = $firstName;
            $words[]       = $lastName;
            $words[]       = $firstName[0] . $lastName;
            $words[]       = $lastName . $firstName[0];
            $words[]       = $email;
            $words[]       = $emailDomain;
            $words[]       = $emailPrefix;
            $words[]       = substr($email, 0, strpos($email, '@'));
            $words[]       = preg_replace('~[^a-zA-Z]~', '', $primaryDomain);
            $words[]       = $primaryDomain;
            break;
        }

        foreach ($this->stores as $store) {
            $host = parse_url($store->getUrl(), PHP_URL_HOST);
            $parts = preg_split('~\W~', $host);
            $parts = array_merge($parts, explode('.', $host));
            $parts = array_filter($parts, array($this, 'filterLength'));
            $words[] = $host;
            $words = array_merge($words, $parts);
        }

        return array_unique($words);
    }

    public function filterLength($elem)
    {
        return (strlen($elem) > 3);
    }
}
