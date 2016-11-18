#!/usr/bin/env bash
set -e

TEST_COMMAND="bash build/vagrant/setup_and_run_tests.sh"
TEST_USER='app'  # Other options: root, vagrant (can sudo while app can not)

HYPERNODE_VAGRANT_RUNNER_REPO="https://github.com/ByteInternet/hypernode-vagrant"
HYPERNODE_VAGRANT_RUNNER_DIR='/tmp/hypernode-vagrant-runner'
PROJECT_DIRECTORY="$(dirname "$(readlink -f "$0")")"


if [ -d "$HYPERNODE_VAGRANT_RUNNER_DIR" ]; then
    echo "Ensuring the hypernode-vagrant-runner is the latest version in $HYPERNODE_VAGRANT_RUNNER_DIR"
    cd "$HYPERNODE_VAGRANT_RUNNER_DIR"
    git clean -xfd
    git pull origin master || /bin/true
    git reset --hard origin/master
    cd -
else
    echo "Creating a new checkout of hypernode-vagrant-runner in $HYPERNODE_VAGRANT_RUNNER_DIR"
    git clone $HYPERNODE_VAGRANT_RUNNER_REPO $HYPERNODE_VAGRANT_RUNNER_DIR
fi;

chmod +x ${HYPERNODE_VAGRANT_RUNNER_DIR}/tools/hypernode-vagrant-runner/bin/start_runner.py
PYTHONPATH=${HYPERNODE_VAGRANT_RUNNER_DIR}/tools/hypernode-vagrant-runner \
    ${HYPERNODE_VAGRANT_RUNNER_DIR}/tools/hypernode-vagrant-runner/bin/start_runner.py \
    --project-path="$PROJECT_DIRECTORY" \
    --command-to-run="$TEST_COMMAND" \
    --user="$TEST_USER" \
    "$@"  # All other arguments. So you can run ./runtests.sh -1 or --help for example

