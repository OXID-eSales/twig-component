#!/bin/bash
set -e
vendor/bin/phpunit \
    -c phpunit.xml \
    --coverage-clover=tests/Reports/coverage_phpunit_unit.xml \
    tests/Unit 2>&1 \
| tee tests/Output/unit_tests.txt
