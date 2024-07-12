#!/bin/bash
set -e
export XDEBUG_MODE=coverage
PHPUNIT="vendor/bin/phpunit"
if [ ! -f "${PHPUNIT}" ]; then
    PHPUNIT="/var/www/${PHPUNIT}"
    if [ ! -f "${PHPUNIT}" ]; then
        echo -e "\033[0;31mCould not find phpunit in vendor/bin or /var/www/vendor/bin\033[0m"
        exit 1
    fi
fi
BOOTSTRAP="../../../tests/bootstrap.php"
if [ ! -f "${BOOTSTRAP}" ]; then
    BOOTSTRAP="../oxideshop-ce/tests"
    if [ ! -f "${BOOTSTRAP}" ]; then
        echo -e "\033[0;31mCould not find bootstrap.php in ../../../tests or ../oxideshop-ce/tests\033[0m"
        exit 1
    fi
fi
"${PHPUNIT}" \
    -c tests/phpunit.xml \
    --bootstrap "${BOOTSTRAP}" \
    --coverage-clover=tests/Reports/coverage_phpunit_unit.xml \
    --log-junit tests/Reports/phpunit-unit.xml \
    tests/Unit 2>&1 \
| tee tests/Output/unit_tests.txt
RESULT=$?
echo "phpunit exited with error code ${RESULT}"
if [ ! -s "tests/Output/unit_tests.txt" ]; then
    echo -e "\033[0;31mLog file is empty! Seems like no tests have been run!\033[0m"
    RESULT=1
fi
cat >failure_pattern.tmp <<EOF
fail
\\.\\=\\=
Warning
Notice
Deprecated
Fatal
Error
DID NOT FINISH
Test file ".+" not found
Cannot open file
No tests executed
Could not read
Warnings: [1-9][0-9]*
Errors: [1-9][0-9]*
Failed: [1-9][0-9]*
Deprecations: [1-9][0-9]*
Risky: [1-9][0-9]*
EOF
sed -e 's|(.*)\r|$1|' -i failure_pattern.tmp
while read -r LINE ; do
    if [ -n "${LINE}" ]; then
        if grep -q -E "${LINE}" "tests/Output/unit_tests.txt"; then
            echo -e "\033[0;31m unit test failed matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "tests/Output/unit_tests.txt"
            RESULT=1
        else
            echo -e "\033[0;32m unit test passed matching pattern ${LINE}"
        fi
    fi
done <failure_pattern.tmp
if [[ ! -s "tests/Reports/coverage_phpunit_unit.xml" ]]; then
    echo -e "\033[0;31m coverage report tests/Reports/coverage_phpunit_unit.xml is empty\033[0m"
    RESULT=1
fi
exit ${RESULT}
