#!/usr/bin/env bash
set -e
export DB=mysql
export MAGENTO_VERSION="magento-mirror-1.9.2.4"
export INSTALL_SAMPLE_DATA=no
export LINTSH=1
export SETUP_DB_USER='app'
export SETUP_DB_PASS=$(grep password ~/.my.cnf | cut -d'=' -f2 | xargs)
export SETUP_DIR='/data/web/'

# correct the magento-root-dir in the composer.json to match Hypernode's docroot
sed -i 's/"magento-root-dir": "htdocs"/"magento-root-dir": "\/data\/web\/public\/'"$MAGENTO_VERSION"'"/g' composer.json

composer config -g repositories.firegento composer https://packages.firegento.com
composer install --prefer-source --no-interaction --ignore-platform-reqs
bash /data/web/public/build/travis/before_script.sh

# Lint PHP code
set +e
find {src,tests} -name "*.php" ! -path '*/String.php' -print0 | xargs -0 -n1 -P8 php -l | grep -v '^No syntax errors detected' 
if [ $? -ne 1 ]; then
    echo "Syntax errors detected"
    exit 1
fi;
set -e

# Run the unit tests
echo "Note: hypernode-vagrant does not support Xdebug at this moment."
echo -e "Tests that require Xdebug will be skipped\n"
vendor/bin/phpunit --debug --stop-on-error --stop-on-failure

# If something is not OK we would have errored out before here
echo "Looks like everything is OK"

