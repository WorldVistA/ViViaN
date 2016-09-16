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
information are found in OSEHRA VistA repository.

Generate Backend Data
^^^^^^^^^^^^^^^^^^^^^^

First, checkout the `OSEHRA VistA-M repository`_.

Next, clone the VistA repository from https://github.com/OSEHRA/VistA::

  $ git clone VistA https://github.com/OSEHRA/VistA

Finally, run the data generation scripts found in the VistA repository's
Utilities/Dox/PythonScripts directory.

FileManGlobalDataParser
~~~~~~~~~~~~~~~~~~~~~~~
The FileManGlobalDataParser.py file’s execution signature is as follows:

.. parsed-literal::

  usage: FileManGlobalDataParser.py [-h] -mr MREPOSITDIR -pr PATCHREPOSITDIR
                                    [-outdir OUTDIR] -gp GITPATH [-all]
                                    fileNos [fileNos ...]

  FileMan Global Data Parser

  positional arguments:
    fileNos               FileMan File Numbers

  optional arguments:
    -h, --help            show this help message and exit
    -outdir OUTDIR        top directory to generate output in html
    -gp GITPATH, --gitPath GITPATH
                          Path to the folder containing git excecutable
    -all                  generate all dependency files as well

  Initial CrossReference Generator Arguments:
    Argument for generating initial CrossReference

    -mr MREPOSITDIR, --MRepositDir MREPOSITDIR
                          VistA M Component Git Repository Directory
    -pr PATCHREPOSITDIR, --patchRepositDir PATCHREPOSITDIR
                          VistA Git Repository Directory

To generate the expected data, use all options, including a directory to save
the output to.  One should utilize the ‘-all’ flag and supply the following file
numbers:

 ======================= =======================
         Numbers              Fileman File
 ======================= =======================
          101                  Protocol
          8994              Remote Procedure
           19                    Option
          779.2              HLO Application
          9.7                  Install
 ======================= =======================

An example command to be run would look like this:

.. parsed-literal::

  $ python FileManGlobalDataParser.py -mr ~/Work/OSEHRA/VistA-M -pr ~/work/osehra/VistA -gp /usr/local/bin -outdir ~/Work/OSEHRA/vivian-out -all 101 8994 19 779.2 9.7

ICRParser
~~~~~~~~~
Creating ICR backend data is a two-part process.

First, execute ICRParser.py. The file’s execution signature is as follows:

.. parsed-literal::
    usage: ICRParser.py [-h] icrfile outJson

    VistA ICR File Parser

    positional arguments:
      icrfile     path to the VistA ICR file
      outJson     path to the output JSON file

    optional arguments:
      -h, --help  show this help message and exit

An example command to be run would look like this:

.. parsed-literal::

  $ python ICRParser.py ICRTest.txt ICRTest.JSON

Next, convert the JSON to html using ICRJsonToHtml.py.
The file’s execution signature is as follows:

.. parsed-literal::
    usage: ICRJsonToHtml.py [-h] -mr MREPOSITDIR -pr PATCHREPOSITDIR
                            icrJsonFile outDir

    VistA ICR JSON to Html

    positional arguments:
      icrJsonFile           path to the VistA ICR JSON file
      outDir                path to the output web page directory

    optional arguments:
      -h, --help            show this help message and exit

    Initial CrossReference Generator Arguments:
      Argument for generating initial CrossReference

      -mr MREPOSITDIR, --MRepositDir MREPOSITDIR
                            VistA M Component Git Repository Directory
      -pr PATCHREPOSITDIR, --patchRepositDir PATCHREPOSITDIR
                            VistA Git Repository Directory

An example command to be run would look like this:

.. parsed-literal::

  $ python ICRJsonToHtml.py -mr ~/Work/OSEHRA/VistA-M -pr ~/work/osehra/VistA ICRTest.JSON ~/Work/OSEHRA/vivian-out

Where the VistA-M and VistA repositories and output directory are the same used
with the FileManGlobalDataParser script.

Link Backend Data with ViViaN
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

After the data parse scripts have been run successfully, a series of
file manipulation steps are necessary to get all of the data into the correct
places. All of these changes are made in the Visual directory of the
Product_Management (ViViaN) repository.

1. Generate a symbolic link  “files” pointing to the output directory specified above.
2. Move all files with the VistAMenu* prefix from Visual/files to the Visual/menus directory.
3. Move ``install_information.json`` from the Visual/files to the Visual directory
4. Update Packages.csv or PackageCategories.json, if needed.

Finally, execute the setup script from the Visual directory:  ``python setup.py``
to generate other JSON and csv files. The script does not take any input
parameters but requires:

* Visual/files directory created in 1.
* Visual/menus directory populated in 2.
* ``Packages.csv``, ``PackageCategories.json``, and ``install_information.json``

The setup script creates or updates: ``menu_autocomplete.json``,
``option_autocomplete.json``, ``PackageInterface.csv``, ``packages.json``,
``packages_autocomplete.json`` and ``install_autocomplete.json``.

Note: ``bff.json`` and ``pkgdep.json`` are also required by the ViViaN pages.
These files are included in the ProductManagement repository and are updated
manually.

.. _WampServer: http://www.wampserver.com/en/
.. _`OSEHRA VistA-M repository`: http://github.com/OSEHRA/VistA-M
