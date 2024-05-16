#!/bin/bash
set -e
vendor/bin/phpunit \
    -c phpunit.xml \
    --coverage-clover=tests/Reports/coverage_phpunit_integration.xml \
    tests/Integration 2>&1 \
| tee tests/Output/integration_tests.txt
