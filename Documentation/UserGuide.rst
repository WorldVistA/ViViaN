======================
ViViaN(TM) User Guide
======================

The **Vi** sualizing **Vi** stA **a** nd **N** amespace (ViViaN) application is
an OSEHRA developed, web-based tool for viewing and browsing relationships
among hierarchical and connected entities.

An interactive visualization of a VistA instance, ViViaN highlights the
internal connections of a VistA system using data mined from a VistA instance
and available KIDS patches

Originally developed to allow browsing of the VistA code base via a tree-based
functional decomposition of the code, ViViaN has expanded to include
tree-based visualizations of VistA menus and the VHA Business Function
Framework categorization; as well as circle plots of the interaction network
among VistA packages.

Homepage
---------

The **Shape Legend** indicates the two items types shown in the tree:
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

Once selected, the path from VistA to the package will be highlighted in red.

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

VistA Menus
------------

The VistA Menus tree represents the menu hierarchy of VistA.

The **Shape Legend** indicates the two item types shown in the tree:
*Menu* and *Option*.

.. figure::
    http://code.osehra.org/content/named/SHA1/62ab82-shapelegend.png
    :align: center
    :alt:  Shape Legend

Hover over any of the entries in the tree to see the option name and the
security key, if one exists.

.. figure::
    http://code.osehra.org/content/named/SHA1/d0db34-hoveroption.png
    :align: center
    :alt:  Hover over an option to see more details

Click on a menu to expand or collapse child options. Click on an option to
navigate to a page with more details.

Use the **Select a top level menu** text box to search for a top level menu.
The *EVE: Systems Manager Menu* is selected by default.

.. figure::
    http://code.osehra.org/content/named/SHA1/3f8335-toplevelmenu.png
    :align: center
    :alt:  Select a top level menu

Use the **Search for an option** text box to search for an option. Search by an
option's full name or by its synonym.

.. figure::
    http://code.osehra.org/content/named/SHA1/e6d598-searchforoption.png
    :align: center
    :alt:  Search for an option

If multiple options with the same name are listed, hover over each option to
see the top level menu the option belongs to.

.. figure::
    http://code.osehra.org/content/named/SHA1/40ac54-searchforoptionmenu.png
    :align: center
    :alt:  Search for an option - Top Level Menu

Note: Options for all menus are can be searched for, not just those in the
currently selected top level menu.

Once selected, the path from the corresponding top level menu to the option
will be highlighted.

Scroll and zoom functionality is available for the menu tree. Click anywhere
on the tree and drag to reposition the tree. Use the mouse wheel to zoom in
and out of the tree.

.. figure::
    http://code.osehra.org/content/named/SHA1/1eef02-navigationbuttons.png
    :align: center
    :alt:  Tree navigation buttons

The tree navigation buttons can also be used to manipulate the tree view.

* **Collapse All** - Collapses all nodes
* **Reset** - Reset to initial state
* **Center** - Center tree

VHA BFF Demo
-------------
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

VistA Package Dependency
-------------------------

Circular Layout
++++++++++++++++
The circle plot captures the interrelationships among VistA packages.

Hover over any of the packages in this graph to see incoming links (dependents)
in one color and the outgoing links (dependencies) in a second. Packages that
are both dependents and dependencies are highlighted in a third color.

.. figure::
    http://code.osehra.org/content/named/SHA1/96f5eb-circularplot.png
    :align: center
    :alt:  Highlighted dependencies and dependents

A colorblind mode is available with a different set of colors.

Click on any of the packages to view package details in Dox.

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

VistA Install
--------------

Install Timeline
+++++++++++++++++
Use the **Install information for a package** text box to search for a package.

.. figure::
    http://code.osehra.org/content/named/SHA1/2184c1-searchpackage.png
    :align: center
    :alt:  Search for package

The date range will automatically be set from the first patch to December 31st
of the current year.

Enter text or click in the date to use the calendar controls to update the
date. Select **Update** to update the graph to the selected date range. Use
**Reset** to go back to the default range.

.. figure::
    http://code.osehra.org/content/named/SHA1/7efbc1-selectdate.png
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
The information in this visualization is not complete. The majority of the
installs may not have dependency information. For the best examples of the
dependency display, select the following "Package" and "Install" pairs:

* Barcode Medication Administration: PSB*3.0*68
* Pharmacy Data Management: PSS*1.0*168
* Scheduling: SD*5.3*581
* Registration: DG*5.3*841
* Integrated Billing: IB*2.0*497

The Install Dependency Tree contains the ability to pan, via a click-and-drag
with the mouse, and zoom, via the scroll wheel.

Use the tree navigation buttons to manipulate the tree.

* **Expand All** - Expands all nodes
* **Collapse All** - Collapses all nodes
* **Reset** - Reset to initial state
* **Center** - Center tree

.. figure::
    http://code.osehra.org/content/named/SHA1/b272fb-navigationbuttons.png
    :align: center
    :alt:  Navigation buttons

Hover over a patch name to see the install name and date. The patch name, and
any duplicates in the tree, will be highlighted.

.. figure::
    http://code.osehra.org/content/named/SHA1/1e8417-hoverpatch.png
    :align: center
    :alt:  Hover patch

CLick on the patch name for more details.

VistA Interfaces
-----------------
The VistA Interfaces menu gives shortcuts to the following Dox tables:
**All HL7**, **All HLO**, **All ICR**, **All Protocols** and **All RPC**.

.. figure::
    http://code.osehra.org/content/named/SHA1/6b3c93-vistainterfaces.png
    :align: center
    :alt:  VistA Interfaces menu

All tables have the same toggling, sorting and searching options. HLO will be
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




