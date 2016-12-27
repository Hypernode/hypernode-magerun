#!/usr/bin/env bash
# Helper to create symlinks into the public directory

NC='\033[0m' # No Color
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'

# Absolute Paths
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
ABS=$( cd "${DIR}/../.." && pwd) # project root
DEST="${ABS}/public/lib/n98-magerun/modules/Byte"

ALIAS="bfm" # Command alias to use

# Colorized echo
cecho(){
    echo -e "${YELLOW}${1}${NC}"
}

check_directory_existence(){
    if [[ ! -d $DEST ]]; then
        cecho "Creating directory"
        mkdir -p $DEST
    fi;
}

check_and_clean_symlinks(){
    local lines=$(find ${DEST} -type l | wc -l)
    if [ ! $lines -eq 0 ]; then
        cecho "Removing old symlinks"
        find $DEST -type l -delete
    fi;
}

create_symlinks(){
    cecho "Creating symlinks"
    ln -s ${ABS}/bin ${DEST}/
    ln -s ${ABS}/src ${DEST}/
    ln -s ${ABS}/tests ${DEST}/
    ln -s ${ABS}/vendor ${DEST}/
    ln -s ${ABS}/n98-magerun.yaml ${DEST}/
}

check_directory_existence
check_and_clean_symlinks
create_symlinks
