# Product Labels MFTF3

### The current version of tests is for Magento CE 2.4.0 only.

In order to receive correct run of image checks it is necessary to store an image (required image is stored with ReadMe.txt file in the same folder of Product Labels module) in magento_root/dev/tests/acceptance/tests/_data folder.
In order to avoid timeout error while tests are running we highly recommend to increase "pageload_timeout:" in magento_root/dev/tests/acceptance/tests/functional.suite.yml

### Release version consists of 14 smoke tests.

### The tests are divided into following groups:
- ProdLab (is used for running of all tests. E.g. vendor/bin/mftf run:group ImpSort -r)
- ProdLabSmoke (is used for running of all smoke tests)
