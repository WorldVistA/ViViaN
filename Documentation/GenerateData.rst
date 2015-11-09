Generating ViViaN(TM) Information
-----------------------------------

Install
^^^^^^^^
The methods for installing the ViViaN tool are quite simple.  The code for the
visualization can be cloned from the OSEHRA-Sandbox/Product_Management
repository: https://github.com/OSEHRA-Sandbox/Product-Management. 

Once the code is acquired, two components are then necessary.  The first is a
service to host the web pages. Any web server that can handle PHP will allow
the pages to be hosted. On Windows, the developers have used WampServer_ as the
host.

The second component is the information that will be used by the ViViaN tool.
It consists of a series of HTML and JSON files.  The HTML files contain the
information about the VistA instance upon which it run.  It captures
information on each package in addition to info about the Remote Procedures,
Options, and the Protocols. The script files necessary to capture this
information are found in a separate version and branch of the OSEHRA VistA
repository.   

Generate Backend Data
^^^^^^^^^^^^^^^^^^^^^^
First, checkout the `OSEHRA VistA-M repository`_.  

Next, clone the VistA repository from https://github.com/OSEHRA-Sandbox/VistA
and switch to the ``VistADataParse`` branch.::

  $ git clone -b VistADataParse http://github.com/OSEHRA-Sandbox/VistA.git

Execute the FileManGlobalDataParser.py file within the
Utilities/Dox/PythonScripts directory. This file’s execution signature is as follows:

.. parsed-literal::

  usage: FileManGlobalDataParser.py [-h] -mr MREPOSITDIR -pr PATCHREPOSITDIR
                                    [-outdir OUTDIR] [-all]
                                    fileNos [fileNos ...]

  FileMan Global Data Parser

  positional arguments:
    fileNos               FileMan File Numbers

  optional arguments:
    -h, --help            show this help message and exit
    -outdir OUTDIR        top directory to generate output in html
    -all                  generate all dependency files as well

  Initial CrossReference Generator Arguments:
    Argument for generating initial CrossReference

    -mr MREPOSITDIR, --MRepositDir MREPOSITDIR
                          VistA M Component Git Repository Directory
    -pr PATCHREPOSITDIR, --patchRepositDir PATCHREPOSITDIR
                          VistA Git Repository Directory

To generate the expected data, use all options, including a directory to save
the output to.  One should utilize the ‘-all’ flag and supply the following file
numbers: ``101``, ``8994``, ``19`` . The numbers  that are appended to the end
of the command are the Fileman numbers for protocol, Remote Procedure Calls,
and Options respectively.  An example command to be run would look like this:

.. parsed-literal::

  $ python FileManGlobalDataParser.py -mr ~/Work/OSEHRA/VistA-M -pr ~/work/osehra/VistA -outdir ~/Work/OSEHRA/vivian-out -all 101 8994 19

Link Backend Data with ViViaN
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
After the FileManGlobalDataParser script has been run successfully, a series of
file manipulation steps are necessary to get all of the data into the correct
places. All of these changes are made in the Visual directory of the
Product_Management (ViViaN) repository.

* Generate a symbolic link  “files” pointing to the output directory specified above.
* Move all files with the VistAMenu* prefix from Visual/files to the Visual/menus directory.
* Update Packages.csv or PackageCategories.json, if needed.
* Execute the setup script from the Visual directory:  ``python setup.py``
  to generate other JSON and csv files. The script does not take any input parameters but requires:

  * Visual/files directory created in 1.
  * Visual/menus directory populated in 2.
  * ``Packages.csv``, ``PackageCategories.json``

The script creates or updates: ``menu_autocomplete.json``, ``option_autocomplete.json``, ``PackageInterface.csv``, and ``packages.json``.

Note: ``bff.json`` and ``pkgdep.json`` are also required by the ViViaN pages.
These files are included in the ProductManagement repository and are updated
manually.

.. _WampServer: http://www.wampserver.com/en/
.. _`OSEHRA VistA-M repository`: http://github.com/OSEHRA/VistA-M