# IliasDemoData unit tests

All test cases and test fixtures belong to this plugin. Test double classes have distinct names and do not declare native ILIAS classes. The lifecycle unit tests alias them **only inside isolated PHPUnit test processes**. They never connect to a production ILIAS database or modify a repository.

## Execute

Run from the ILIAS installation root with the respective supported PHP runtime:

```sh
php vendor/composer/vendor/bin/phpunit --no-configuration \
  --bootstrap vendor/composer/vendor/autoload.php \
  components/Base3/IliasDemoData/test
```

The same test source is intended for PHPUnit 11.5 and PHPUnit 12.5. Data providers use attributes, not removed docblock metadata. The suite does not use deprecated PHPUnit mock configuration APIs or depend on a global ILIAS test stub being loaded during Composer build.

## Coverage gate

```sh
sh components/Base3/IliasDemoData/test/verify-coverage.sh
```

`verify-coverage.sh` runs the suite against `src/`, collects Clover coverage and exits nonzero unless **all measured production statements** are covered. Configure `PHPUNIT_BIN` if the ILIAS installation uses a different PHPUnit executable. A PCOV or Xdebug coverage driver is required; without one the gate deliberately fails rather than reporting a false success.

This test suite checks both the positive lifecycle and safety boundaries: ownership markers, native administrator checks, persisted object and user membership checks, role and repository deletion verification, orphan cleanup, rollback on creation errors, HTML/JSON rendering, CSRF and feature access, and the static content corpus.

Test coverage cannot verify behavior of a running ILIAS installation or substitute for a separate integration test. The actual version-specific ILIAS classes and migrations remain owned by ILIAS. These unit tests are intentionally isolated from that host.
