#!/usr/bin/env bash
set -e
export DB=mysql
export MAGENTO_VERSION="magento-mirror-1.9.2.4"
export INSTALL_SAMPLE_DATA=no
export LINTSH=1
export SETUP_DB_USER='app'
export SETUP_DB_PASS=$(grep password ~/.my.cnf | cut -d'=' -f2 | xargs)
export SETUP_DIR='/data/web/'

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
target_directory="${SETUP_DIR:-./}${MAGENTO_VERSION}"
export N98_MAGERUN_TEST_MAGENTO_ROOT="${target_directory}"
vendor/bin/phpunit --debug --stop-on-error --stop-on-failure

# If something is not OK we would have errored out before here
echo "Looks like everything is OK"

