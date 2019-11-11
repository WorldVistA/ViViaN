=========================
ViViaN and DOX User Guide
=========================

The **Vi** sualizing **Vi** stA **a** nd **N** amespace (ViViaN) application is
an OSEHRA developed, interactive, web-based tool for viewing and browsing
relationships in a VistA instance. ViViaN highlights internal connections and
hierarchies using data mined from a VistA instance of interest and KIDS
patches.

Originally developed to allow browsing of the VistA code base via a tree-based
functional decomposition of the code, ViViaN has expanded to include
tree-based visualizations of VistA menus and the VHA Business Function
Framework categorization; as well as circle plots of the interaction network
among VistA packages.

The VistA Cross Reference Documentation (DOX) is a companion application to
ViViaN. Also developed by OSEHRA, the DOX pages dive deeper into VistA and
provide direct dependency information among packages and FileMan files,
between globals and routines, and direct call/caller graphs and source code
for individual routines.

Whereas ViViaN provides interactive visualizations, DOX is text and table
based. The DOX pages are generated from the results of XINDEX and FileMan Data
Dictionary utilities running against the same VistA codebase as ViViaN.

OSHERA's instances of  ViViaN and DOX are based on the FOIA release found at
https://github.com/OSEHRA/VistA-M. It is also possible to create your own
ViViaN and DOX based on a local VistA instance. Detailed instructions on this
process are available at
https://github.com/OSEHRA/VistA/blob/master/Documentation/generateViViaNAndDox.rst

To visit the version of ViViaN maintained by OSEHRA, navigate to
https://code.osehra.org/vivian. While exploring ViViaN, you may
find links into the DOX pages. To access the DOX pages directly, navigate
to: https://code.osehra.org/dox.

The navigation bar at the top of ViViaN allows users to navigate to the
different pages available in the ViViaN tool. The remainder of this document
will describe each page in detail.

Visualization Open Source Project Group
---------------------------------------

ViViaN development is led by the Visualization Open Source Project Group.
From ViViaN, click on 'Join the Visualization Working Group' to join the
group or find the next meeting time - both technical and non-technical
participants are welcome!

Package Tree
------------

Select the ViViaN icon to display the Package Tree. This is the default page
that is shown when first navigating to ViViaN.

The **Shape Legend** indicates the two item types shown in the tree:
*Package Category* and *Package*.

.. figure::
   http://code.osehra.org/content/named/SHA1/d439d8-shapelegend.png
   :align: center
   :alt:  Shape Legend

Click on a *Package Category* to expand or collapse all child nodes. Hover
over a *Package* to show more information.

.. figure::
    http://code.osehra.org/content/named/SHA1/a1de42-hoverpackage.png
    :align: center
    :alt:  Hover over Package Kernel to show more information

Click on a *Package* to display a modal dialog box.

.. figure::
    http://code.osehra.org/content/named/SHA1/f2f0e7-modaldialog.png
    :align: center
    :alt:  Kernel modal dialog

Use the **Distribution Filter Menu** to highlight which packages are available
in a specific distribution. *All* distributions are selected by default.

.. figure::
    http://code.osehra.org/content/named/SHA1/539d44-distributionfiltermenu.png
    :align: center
    :alt:  Distribution Filter Menu - OSEHRA VistA packages only

Use the **Search for a package** text box to search for a specific package.

.. figure::
    http://code.osehra.org/content/named/SHA1/665327-searchforpackage.png
    :align: center
    :alt:  Search for a package

Once selected, the path from the VistA root to the package will be
highlighted in red.

.. figure::
    http://code.osehra.org/content/named/SHA1/c2c16b-caremanagementpackage.png
    :align: center
    :alt:  Care Management package

Use the tree navigation buttons to manipulate the tree:

* **Expand All** - Expands all nodes
* **Collapse All** - Collapses all nodes
* **Reset** - Reset to initial state

.. figure::
    http://code.osehra.org/content/named/SHA1/b6fa73-treenavigationbuttons.png
    :align: center
    :alt:  Tree Navigation Buttons

Menus
-----

The VistA Menus trees represent the menu hierarchy of VistA. There are two
subpages - VistA Option Menus and VistA Protocol Menus. The options for both
pages are the same. In this guide, we'll look at the VistA Option Menus.

The **Shape Legend** indicates the two item types shown in the tree:
*Menu* and *Entry* (option or protocol).

.. figure::
    http://code.osehra.org/content/named/SHA1/6fabd0-shapelegend.png
    :align: center
    :alt:  Shape Legend

Hover over any of the entries in the tree to see the entry name and the
security key, if one exists.

.. figure::
    http://code.osehra.org/content/named/SHA1/d0db34-hoveroption.png
    :align: center
    :alt:  Hover over an entry to see more details

Click on a menu to expand or collapse child entries. Click on an entry to
navigate to a page with more details.

Use the **Select a top level menu** text box to search for a top level menu.

.. figure::
    http://code.osehra.org/content/named/SHA1/3f8335-toplevelmenu.png
    :align: center
    :alt:  Select a top level menu

Use the **Search for an entry** text box to search for an entry. Search by an
entry's full name or by its synonym.

.. figure::
    http://code.osehra.org/content/named/SHA1/904ab7-searchforoption.png
    :align: center
    :alt:  Search for an entry

If multiple entries with the same name are listed, hover over each entry to
see the top level menu the entry belongs to.

.. figure::
    http://code.osehra.org/content/named/SHA1/505291-searchforoptionmenu.png
    :align: center
    :alt:  Search for an entry - Top Level Menu

Note: Entries for all top level menus can be searched for, not just those in the
currently selected top level menu.

Once selected, the path from the corresponding top level menu to the entry
will be highlighted.

Scroll and zoom functionality is available for the menu trees. Click anywhere
on the tree and drag to reposition the tree. Use the mouse wheel to zoom in
and out of the tree.

Other options that can also be used to manipulate the tree view include:

.. figure::
    http://code.osehra.org/content/named/SHA1/1eef02-navigationbuttons.png
    :align: center
    :alt:  Tree navigation buttons
    
* **Collapse All** - Collapses all nodes
* **Reset** - Reset to initial state
* **Center** - Center tree

VHA BFF & Requirements
----------------------
The VHA Business Function Framework (BFF) is a hierarchical construct that
describes the VHA business functions or major service areas within each core
mission Line of Business (LoB) and serve as logical groupings of activities.
Subfunctions represent the logical groupings of sub-activities needed to
fulfill each VHA business function.  This demo is based on BFF version 2.12.

Click on an item's text to bring a modal dialog box with detailed description
and commentary.

.. figure::
    http://code.osehra.org/content/named/SHA1/c4c045-modaldialog.png
    :align: center
    :alt:  Conduct Health Care Research dialog

Package Dependency
------------------

Circular Layout
++++++++++++++++
The circle plot captures the interrelationships among VistA packages.

Hover over any of the packages in this graph to see incoming links (dependents)
in one color and the outgoing links (dependencies) in a second. Packages that
are both dependents and dependencies are highlighted in a third color.

.. figure::
    http://code.osehra.org/content/named/SHA1/c38732-circularplot.png
    :align: center
    :alt:  Highlighted dependencies and dependents

A colorblind mode is available with a different set of colors.

Click on any of the packages to view package details in Dox.

Packages are sorted into groups. Hover over each bar to see the group name.

.. figure::
    http://code.osehra.org/content/named/SHA1/cf4773-circularplotgroup.png
    :align: center
    :alt:  Package Groups

Bar Chart
++++++++++
Two options are available in this screen: Dependency Chart and Stats Chart. Use
the chart type control to toggle between the options.

.. figure::
    http://code.osehra.org/content/named/SHA1/b40440-barchart.png
    :align: center
    :alt:  Chart type toggle

Dependency Chart
~~~~~~~~~~~~~~~~~
The Dependency Chart shows the same information as the circular plot.

.. figure::
    http://code.osehra.org/content/named/SHA1/cc94dc-dependencychart.png
    :align: center
    :alt:  Dependency Chart

Use the drop-down box to sort the package order.

.. figure::
    http://code.osehra.org/content/named/SHA1/7a8a5e-sortpackages.png
    :align: center
    :alt:  Sort Packages

Click on a package name to navigate to the Dox page for that package.

Hover over the bars for a summary.

.. figure::
    http://code.osehra.org/content/named/SHA1/e915e3-packagesummary.png
    :align: center
    :alt:  Package summary

Stats Chart
~~~~~~~~~~~~
Use this view to see statistics (number of routines, files and fields) for
each package.

.. figure::
    http://code.osehra.org/content/named/SHA1/3ab74e-packagestatistics.png
    :align: center
    :alt:  Package Statistics

Use the sorted by control to determine the order that the packages are
displayed.

.. figure::
    http://code.osehra.org/content/named/SHA1/b66f79-sortpackagestatistics.png
    :align: center
    :alt:  Sort Package Statistics

Click on a package name to navigate to the Dox page for that package.

Hover over the bars for a summary.

.. figure::
    http://code.osehra.org/content/named/SHA1/219e7d-packagestatisticssummary.png
    :align: center
    :alt:  Package Statistics Summary

Force Directed Graph
++++++++++++++++++++

Similar to the circular plot, the Force Directed Graph shows relationships between VistA packages.

Select one or more groups from the list to display. *Application Infrastructure* (shown in blue
below) is selected by default, but can be unselected.

.. figure::
    http://code.osehra.org/content/named/SHA1/3c7f50-selectpackage.png
    :align: center
    :alt:  Select one or more groups

Use the **Search for a package** text box to find a package in any of the selected groups.
Alternatively, click on any node to select a package. Only packages in the selected groups with
connections to the selected package will be shown. Incoming links (dependents) are highlighted in one
color and outgoing links (dependencies) are highlighted in a second. Connections that are both
dependents and dependencies are highlighted in a third color. Toggle the highlight colors using the
**Colorblind Mode** checkbox.

.. figure::
    http://code.osehra.org/content/named/SHA1/285872-searchforpackage.png
    :align: center
    :alt:  Select a package

Hover over any node to see more information about that package's interactions.

.. figure::
    http://code.osehra.org/content/named/SHA1/d0c4bc-hoverpackage.png
    :align: center
    :alt:  Hover over package to see detailed information

Install
-------

Install Timeline
+++++++++++++++++
Use the **Install information for a package** text box to search for a package.

.. figure::
    http://code.osehra.org/content/named/SHA1/a4deaf-searchpackage.png
    :align: center
    :alt:  Search for package

The date range will automatically be set from the first patch to December 31st
of the current year.

Click and drag under the timeline to select a date range. Once a range has been
selected, click and drag to adjust the slider position. Use **Reset** to
return to default date range.

.. figure::
    http://code.osehra.org/content/named/SHA1/92fa2f-timerange.png
    :align: center
    :alt:  Select date range

Hover over a bar to see more details about the patch.

.. figure::
    http://code.osehra.org/content/named/SHA1/d2b77b-hoverpatch.png
    :align: center
    :alt:  Patch details

Taller bars will be larger patches with more installed files and routines than
shorter bars. The bar colors do not represent anything. Background color
changes signify a change in the major patch number. Click on a bar to navigate
to a detailed description of the patch.

Install Dependency Tree
++++++++++++++++++++++++
Give this visualization a try by selecting any of the following "Package" and
"Install" pairs:

* Barcode Medication Administration: PSB*3.0*68
* Pharmacy Data Management: PSS*1.0*168
* Scheduling: SD*5.3*581
* Registration: DG*5.3*841
* Integrated Billing: IB*2.0*497

.. figure::
    http://code.osehra.org/content/named/SHA1/e40b0c-selectpatch.png
    :alt:  Select a patch

Duplicate patches are indicated with a red diamond while unique patches are
green triangles or circles.

The information on this visualization is not guaranteed to be complete, due to
display limits, trees may be truncated when they reach a maximum number of
levels.

The Install Dependency Tree contains the ability to pan, via a click-and-drag
with the mouse, and zoom, via the scroll wheel.

Use the tree navigation buttons to manipulate the tree.

* **Reset** - Reset to initial state
* **Center** - Center tree

Hover over a patch name to see the install name and date. The patch name, and
any duplicates in the tree, will be highlighted.

.. figure::
    http://code.osehra.org/content/named/SHA1/3f8b14-hoverpatch.png
    :align: center
    :alt:  Hover patch

VistA Interfaces
-----------------
The VistA Interfaces menu gives shortcuts to the following tables:
**HL7**, **HLO**, **ICR**, **Protocols** and **RPC**.

.. figure::
    http://code.osehra.org/content/named/SHA1/6b3c93-vistainterfaces.png
    :align: center
    :alt:  VistA Interfaces menu

All tables have the same toggle, sort, search and download options. HLO will be
used as an example.

.. figure::
    http://code.osehra.org/content/named/SHA1/977564-allhlolist.png
    :align: center
    :alt:  All HLO List

Toggle
++++++

The **Toggle Columns** button in the top-left of the table expands to display a
list of all columns. Click on a column name to toggle visibility. By default,
if available for the selected table, the *General Description* column is
hidden.

Use the **Reset Columns** button to return to the original column
configuration.

Sort
++++

Click on a column header to sort the table by that column's contents. Press the
shift key to sort by multiple columns.

.. figure::
    http://code.osehra.org/content/named/SHA1/a92c67-sortcolumn.png
    :align: center
    :alt:  Sort column

Search
++++++

The **Search** box in the top-right of the table will perform a global search
across *all* columns, even if they are hidden. If a match is found in any
column, then the whole row is matched and shown in the result set. Search
individual columns using the search boxes or dropdown boxes underneath each
column. Rows that match all global *and* individual searches are displayed. Use
the **Clear Search** button to clear all searches.

.. figure::
    http://code.osehra.org/content/named/SHA1/ff9b05-search.png
    :align: center
    :alt:  Search

Both the global and individual searches provide the following abilities:

1. Match words out of order. For example, FILEMAN VA will match rows containing
   the words *FILEMAN* and *VA*, regardless of the order or position that they
   appear in the table.
2. Partial word matching. For example, *Act* will match *Active*.
3. Search for an exact phrase by enclosing the search text in double quotes.
   For example, *"Potential List"* will match only text which contains the
   phrase *Potential List*. It will not match *Potential Unsafe Orders List*.

Download
++++++++

Use the **CSV** and **PDF** buttons to download the currently displayed table
as a CSV or PDF document.

Name and Number
---------------
The Name and Number menu gives access to the Name and Number Listing tables.
Both tables have the same toggle, sort, search and download options as the
VistA Interface tables. See that section for details.

Classify Data
-------------

This is the only ViViaN page that accepts input directly from users. Upload
a file in the specified JSON format or load an existing file from the dropdown.

.. figure::
    http://code.osehra.org/content/named/SHA1/0c80fa-uploadfile.png
    :alt:  Load existing file


Once the file has been loaded, select a field from the second dropdown.

.. figure::
    http://code.osehra.org/content/named/SHA1/a72b50-selectfield.png
    :alt:  Select field

By default, a pie chart representing the selected data is displayed. Hover over a
section to see summary data. Click on a section to see detailed information
about the object.

.. figure::
    http://code.osehra.org/content/named/SHA1/8c669e-hoversection.png
    :alt:  Summary Data

Use the **Switch Display** button to switch to a table view of the data. The
standard table options as described in VistA Interfaces are available.

DSS VxVistA
----------
Under the **FOIA VistA** menu, select **DSS VxVistA** to view a subsection of
ViViaN pages built from a DSS VxVistA instance.

VA Visualizations
-----------------

This menu provides links to the VHA Business Information and Hybrid models.

