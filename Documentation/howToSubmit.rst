================================
How to Contribute to ViViaN(TM)
================================

When making bug-fixes or improvements to ViViaN, changes may be required to
the `OSEHRA VistA repository`_ or to the files in this repository.

In general, the scripts that create and organize the data behind the ViViaN and
DOX pages can be found in the `Utilities\\Dox\\PythonScripts` directory of the
OSEHRA VistA repository. Follow the `OSEHRA VistA Contributor Instructions`_ to
update the generation scripts. This document describes how to develop and
submit changes to the ViViaN visualizations themselves.

Fork the repository
---------------------
In order to generate a pull request, the Product-Management repository must
first be forked via the Github website. This process generates a copy of the
repository under the user's Github account where the user can push and pull
branches . The fork is generated from the ViViaN repository web page, which can
be found at https://github.com/OSEHRA-Sandbox/Product-Management. Once there,
click on the the "Fork" button in the top right corner.

.. figure::
   http://code.osehra.org/content/named/SHA1/45b22c-vivianForkHighlight.png
   :align: center
   :alt:  Github page of ViViaN repository with 'Fork' button highlighted

Once the forking has completed, the browser will be directed to the newly
created repository. This new repository will be found under the user’s account
and will present the Git connection information to push and pull from the new
repository in the top menu bar.

.. figure::
   http://code.osehra.org/content/named/SHA1/5c2a21-vivianInfoHighlight.png
   :align: center
   :alt:  Github page of forked repository with connection information highlighted

This connection information can then used to clone an instance of the
Product-Management repository to perform the development work on.  The
OSEHRA-Sandbox will be used as the ``origin`` repository while the new
fork will be added as an additional remote. If you already have an existing
clone of the OSEHRA-Sandbox repository, you can skip to the ``Adding Remote``
section.

Cloning from OSEHRA-Sandbox
++++++++++++++++++++++++++++

Execute a ``git clone`` command to acquire a copy of the Product-Management
repository.

.. parsed-literal::

  softhat@softhatvm /d/wamp/www
  $ git clone git://github.com/OSEHRA-Sandbox/Product-Management.git


Adding Remote
+++++++++++++

Now that there is a copy of the Product-Management repository, the newly forked
repository can be added as a new Git remote via a ``git remote add`` command.
The remote corresponds to a repository that can be accessed(pushed to or pulled
from) by the user. In its most basic form, the git remote add command takes two
arguments:

.. parsed-literal::

  $ git remote add
  usage: git remote add [<options>] <name> <url>
  <snip>

The name is a string that will identify the remote to the user while the url
should be one of the connection arguments taken from the information bar of the
new repository. An example of the git remote command is shown below:

.. parsed-literal:

  softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)
  $ git remote add personal git://github.com/softhat/Product-Management.git


Workflow
--------
Our collaboration workflow, based on topic branches, consists of three main
steps:

1. Local Development

  a. Update
  b. Create a Topic
  c. Run Tests

2. Code Review

  a. Share a Topic
  b. Revise a Topic

3. Integrate Changes

  a. Merge a Topic (requires authorization by OSEHRA)

Update
+++++++

Update your local master branch:

.. parsed-literal::

  $ git checkout master
  $ git pull

Create a Topic
+++++++++++++++

All new work must be committed on topic branches. Name topics like you might
name functions: concise but precise. A reader should have a general idea of the
feature or fix to be developed given just the branch name.

To start a new topic branch:

.. parsed-literal::

  $ git fetch origin
  $ git checkout -b add_new_file origin/master

Edit files and create commits (repeat as needed):

.. parsed-literal::

  $ edit file1 file2 file3
  $ git add file1 file2 file3
  $ git commit

The commit message should describe the reason behind the change and any
necessary information to setup and use the change.

Run tests
+++++++++

Run existing tests and add new tests, if needed. See Testing_ for detailed
instructions. Commit any changes to the development branch.

Share a Topic
++++++++++++++

The development branch needs be pushed to the personal fork.

In this example, the branch "add_new_file" will be pushed to the personal fork
generated earlier.

.. parsed-literal::

  softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)
  $ git push personal add_new_file

The method for submitting a pull request via GitHub is fairly straightforward.
Once the branch has been pushed to a personal fork, click on the "New pull 
request" button found on the webpage of the user’s fork.

.. figure::
   http://code.osehra.org/content/named/SHA1/2f2763-vivianNewPullRequestHighlight.png
   :align: center
   :alt:  Github page of forked repository with connection information highlighted

This will open a webpage where the user is asked to select four bits of
information regarding which branches and forks will be used as the components
of the pull request. The base information is where the new commits are going to
merged to, while the head fork and compare information is where the commits
will be taken from. Continuing with the examples from above, we are going to
request that the add_new_file branch found on the softhat/Product-Management
fork be merged into the master branch of the OSEHRA-Sandbox fork:

 ============= ==================================
   Parameter                   Value
 ============= ==================================
  base fork     OSEHRA-Sandbox/Product-Management
  base branch               master
  head fork         softhat/Product-Management
  compare              add_new_file
 ============= ==================================

**When submitting the pull request, ensure that the ``base`` branch that is the
target for the merge is the ``master`` branch.**

When the information is set, click on the Create pull request button to
generate the request. Below is an example using a different development 
branch which shows both the places where the information is set and the 
button to generate the pull request.

.. figure::
   http://code.osehra.org/content/named/SHA1/c56600-vivianCreatePullRequestHighlight.png
   :align: center
   :alt:  Github page of forked repository with connection information highlighted


When the pull request has been submitted, the change will be reviewed by
the members of the OSEHRA Visualization Open Source Project Group. Members of 
the project group, owners of the repository, and general public may leave 
comments and suggestions for improvement.

Revise a Topic
+++++++++++++++

If a fix or further work is requested, the submitter should return to the
development environment.  Any changes made should be made on the same 
development branch as the original one. As long as the pull request is open, 
any commits pushed to the pull request branch on Github will be automatically 
included as part of the pull request.

Merge a Topic
+++++++++++++

After a topic has been reviewed and approved in GitHub it may be submitted to
the upstream repository.

**Only developers authorized by OSEHRA may perform this step.**

At the end of the OSEHRA Visualization Open Source Project Group sprint, all
of the approved updates will be merged to the master branch.


.. _`OSEHRA VistA repository`: https://github.com/OSEHRA/VistA/blob/master
.. _Testing: testing.rst
.. _`OSEHRA VistA Contributor Instructions`: https://github.com/OSEHRA/VistA/blob/master/Documentation/ContributorInstructions.rst
