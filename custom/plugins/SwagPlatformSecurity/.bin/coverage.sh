#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
CI_PROJECT_DIR=$(realpath ${DIR}/../../../../)

php -d pcov.enabled=1 -d pcov.directory=$CI_PROJECT_DIR \
            $CI_PROJECT_DIR/vendor/bin/phpunit \
            --configuration $CI_PROJECT_DIR/custom/plugins/SwagPlatformSecurity/phpunit.xml \
            --log-junit build/artifacts/phpunit.junit.xml \
            --colors=never \
            --coverage-clover build/artifacts/phpunit.clover.xml \
            --coverage-html build/artifacts/phpunit-coverage-html \
            --coverage-text
