ViViaN(TM) Testing
------------------

The testing files for ViViaN can be found in the 'test' directory of the
Product_Management (ViViaN) repository:

https://github.com/OSEHRA-Sandbox/Product-Management/tree/master/Visual/test.

Tests
+++++

Each 'test' python file contains tests for one ViViaN or DOX page:

test_about.py
  'About' dialog

test_bff_demo.py
  'VHA BFF & Requirement'

test_icrtable.py
  'All-ICR' table

test_index.py
  'ViViaN'

test_installdep.py
  'Install Dependency Tree'

test_installscale.py
  'Install Timeline'

test_links.py
  Clicks on all buttons in the top navigation bar

test_menus.py
  'VistA Menus'

test_pkg_dependency.py
  'VistA Package Dependency' (Circular Layout)

test_pkg_dependency_chart.py
  'VistA Package Dependency' (Bar Chart)


Set-up
+++++++

Prerequistes
~~~~~~~~~~~~
* Python 2.7 or 3 (preferred)
* CMake 2.8+ (https://cmake.org/download/)
* Firefox or Chrome
* Latest version of Selenium Python and driver to interface with the chosen
  browser (e.g. geckodriver or chromedriver). Follow steps 1.1 - 1.4 in the
  Selenium Python installation documentation
  (http://selenium-python.readthedocs.io/installation.html)

**Note**: Firefox 46 or older must be used with Selenium 3.4 or older.
Some newer versions of FireFox and geckodriver have issues and are not
recommended. The test suite is known to work with: Firefox 60.0.2, Selenium 3.6
and geckodriver 20.1.

**Note**: Tests can be run on Linux or Windows

Set-up Tests
~~~~~~~~~~~~

Run the following commands to set-up the testing suite:

.. parsed-literal::

  mkdir vivian_test
  cd vivian_test
  cmake <path to ViViaN source>/Visual/test

**Note**: The <vivian_test> directory can be created anywhere that is
convenient (best practice is not in the ViViaN source directory).

Use ccmake or cmake gui to configure tests.

*WEB_ROOT*

* The default value is: https://code.osehra.org
* To test local changes, point at a test instance (e.g http://localhost)

*BROWSER*

* FireFox (default)
* Chrome

List Tests
++++++++++

Run the following command to see a list of all available tests:

.. parsed-literal::

  $ ctest -N
  Test project /home/betsy/vivian/vivian_test
    Test  #1: VIVIAN_test_about
    Test  #2: VIVIAN_test_bff_demo
    Test  #3: VIVIAN_test_icrtable
    Test  #4: VIVIAN_test_index
    Test  #5: VIVIAN_test_installdep
    Test  #6: VIVIAN_test_installscale
    Test  #7: VIVIAN_test_links
    Test  #8: VIVIAN_test_menus
    Test  #9: VIVIAN_test_pkg_dependency
    Test #10: VIVIAN_test_pkg_dependency_chart


Run All Tests
+++++++++++++

To run all of the tests:

.. parsed-literal::

  ctest

Run One Test
++++++++++++

To run just one test:

.. parsed-literal::

  ctest -R about

Test Results
++++++++++++

A list of failing tests is printed to the screen and can be found here:
<vivian_test>/Testing/Temporary/LastTestsFailed.log

More details are available here:
<vivian_test>/Testing/Temporary/LastTest.log

TroubleShooting
~~~~~~~~~~~~~~~

* If using Chrome, make sure chromedriver is up-to-date and on the path.
  See https://sites.google.com/a/chromium.org/chromedriver/downloads
