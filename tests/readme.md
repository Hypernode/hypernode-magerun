Hypernode Magerun Addons - Tests
================================

Within this folder PHPUnit tests are included. 
These tests are automatically ran by travis on push and pull request in various environment setups related to Hypernode.

# Configuring your local environment

The local environment requires:

* an environment variable pointing to a Magento setup

        export N98_MAGERUN_TEST_MAGENTO_ROOT=htdocs
* composer dev dependencies
        
        composer install --dev
        
* Magento installation
        
        magerun install --installationFolder=htdocs --useDefaultConfigParams=yes

        
## Setup script

For convenience, the `test_setup.sh` script can be used to initialize this setup. It will install Magento into the htdocs folder and setup the environment variable.
Make sure the database credentials in this script match your environment.

## Example

```bash
composer install --dev && ./build/local/test_setup.sh && vendor/bin/phpunit
```
