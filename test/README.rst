--------------------------------
Coverage via JSCover_ and CDash
--------------------------------

**This functionality requires a version of CMake that is 3.2 or later**

The VistA Product Management Visualization repository now has the capability
to test the web interface using the Selenium_ tool.  Using one mode of JSCover,
instrumented Javascript files can be generated and used to calculate the
coverage of a set of Selenium tests.

After downloading_ and unpacking the zip file, we use the JSCover-all.jar file
found in the ``target/dist/`` directory. For this example, we will assume that
the JSCover files have been unpacked into the ``~/jscover`` directory, with the
ViViAN repository being found in ``~/Product_Management/Visual``.

File Set up 
^^^^^^^^^^^^^^^^^^^^^

Instrumenting JavaScript
$$$$$$$$$$$$$$$$$$$$$$$$
The ``filesystem`` mode is executed by running that JSCover-all.jar file with
the ``-fs`` flag.  The program signature can be printed by running the program
with an additional ``-h`` argument flag.


.. parsed-literal::
  $ java -jar ~/jscover/bin/JSCover-all.jar -fs -h
    Usage: java -jar JSCover-all.jar -fs [OPTION]... SOURCE-DIRECTORY DESTINATION-DIRECTORY
    Instrument JavaScript with code coverage information.

    Options:
          --js-version=VERSION  use the specified JavaScript version
          --no-instrument=PATH  copy but do not instrument PATH
          --exclude=PATH        don't copy from source/PATH
          --branch              collect branch coverage data
      -h, --help                display this help and exit

An example with full paths would look like the following:

.. parsed-literal::

 /usr/bin/local/java -jar ~/jscover/target/dist/JSCover-all.jar -fs --no-instrument=lib --exclude=test ~/Product_Management/Visual/ ~/Product_Management/instrumented/ 
 
This would copy the file structure from the ``~/Product_Management/Visual``
directory into the ``~/Product_Management/instrumented`` directory with the
exception of the ``~/Product_Management/Visual/test`` directory. It would
instrument all Javascript files found in the source directory with the 
exception of those under the ``~/Product_Management/Visual/lib`` directory.
It will also write a series of files which contain a variety of functions for
the JSCover tool.  There is one file in particular that will be necessary to
save the coverage results, ``jscoverage.js``.

This new ``instrumented`` directory should directory that is served via Apache
or one of the other web servers and should be the instance which the Selenium
scripts will access for testing.  
 
Saving Coverage Results
$$$$$$$$$$$$$$$$$$$$$$$

Now that the JavaScript files are instrumented, the next step is to generate
the HTML elements which allow you to save the information from the new arrays.
The ViViAN menu bar should be updated to contain two links: one to run the
coverage summary and another to save the coverage to a file on the system.

The first "Run Coverage" link should execute a JSCover JavaScript function
which summarizes and writes out the results.  The ``jscoverage.js`` file
contains the function ``jscoverage_serializeCoverageToJSON`` which reads the
instrumented arrays into a JSON formatted file.  The output of this function
can be used to generate a blob object which can then be set as the download
target for the second link.

This functionality exists in the ViViAN code already, it can be found in the
``vivian_osehra_image.php`` file starting at `line 44`_


CDash Submission
^^^^^^^^^^^^^^^^^

For submission to a CDash instance that has the XML_handler for JSCover JSON
files, this repository contains script named ``vivian_common.cmake``.  This
file will potentially generate a new checkout of the repository, configure and
perform the testing, then submit both the results and the coverage files to the
CDash instance.

The header of the ``vivian_common.cmake`` file contains the structure of a
different CMake file which will be used to configure the checkout and setup of
the result of the script.  This file should be generated in the same directory
as the ``vivian_common.cmake`` script.  There are three variables which are
required to be set prior to running the common script inside this local CMake
file:

The two ViViAN CMake variables to set are ``VIVIAN_WEB_ROOT`` and
``VIVIAN_COVERAGE``:

+------------------+-------------------+---------------------------------+
|   Variable       |     Value         |             Example             |
+==================+===================+=================================+
| VIVIAN_WEB_ROOT  | Web address of    |  http://code.osehra.org/vivian/ |
|                  | instance to test  |                                 |
+------------------+-------------------+---------------------------------+
| VIVIAN_COVERAGE  | Execute Selenium  |                                 |
|                  | commands to save  |                ON               |
|                  | coverage          |                                 |
|                  |                   |                                 |
+------------------+-------------------+---------------------------------+

The other variable to be set is the ``dashboard_root_name`` which should be set
to a directory where the ViViAN code will be cloned/update in upon running the
CTest script.  An example of the variables being set can be found in the header
as well.

Once these variables are set in the platform file, execute the local file using
CTest.  This local file will call the ``vivian_common.cmake`` file when
necessary. If the file is named ``local.cmake``, the command would look like
this:

.. parsed-literal::

  $ ctest -S local.cmake 
  
For an example to follow, see the Notes of one of the Thessia/Win32-ViViAN
submissions on the OSEHRA CDash instance.  Each page will contain copies of the
files used to execute the ViViAN submission process:

  http://code.osehra.org/CDash/viewNotes.php?buildid=22471


.. _JScover: https://github.com/node-modules/jscover
.. _downloading: https://github.com/node-modules/jscover
.. _Selenium: http://www.seleniumhq.org/
.. _Blob: http://www.w3.org/TR/FileAPI/#blob
.. _`line 44`: ../vivian_osehra_image.php#44