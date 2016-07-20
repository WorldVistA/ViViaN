================================
How to Contribute to ViViaN(TM)
================================

The ViViaN visualization is gathering steam and is now gathering submissions
from a wider range of developers. This introduces the need to codify the
process of submitting an update or bug fix to the ViViaN repository. OSEHRA
hosts two different copies of the ViViaN tool: one is considered the
demonstration instance, which utilizes the vivian-demo branch, and the other
is the main instance which follows the master branch of the repository.
Changes to the ViViaN tool are expected to be made a part of the demonstration
instance prior to being merged into the master branch. This document will
describe the steps to submit a change to the demonstration instance of the
ViViaN visualization and the process by which that change is moved to the
master branch.

Fork the repository
---------------------
In order to generate a pull request, the Product-Management repository must
first be forked via the Github website. This process generates a copy of the
repository under the user's Github account where the user can push and pull
branches . The fork is generated from the ViViaN repository web page, which can
be found at https://github.com/OSEHRA-Sandbox/Product-Management. Once there,
click on the the "Fork" button in the top left corner.

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

This connection information is then used to clone an instance of the
Product-Management repository to perform the development work on.  The
OSEHRA-Sandbox will be used as the ``origin`` repository while the new
fork will be added as an additional remote. If you already have an existing
clone of the OSEHRA-Sandbox repository, you can skip to the ``Adding Remote``
section.

Cloning from OSEHRA-Sandbox
++++++++++++++++++++++++++++

In order to be able to push to the ``vivian-demo`` branch, the OSEHRA-Sandbox
repository must be one of the Git remotes.  We will clone from the the
OSEHRA-Sandbox to make it easier keep the ``master`` and ``vivian-demo``
branches up-to-date.  Execute a ``git clone`` command to acquire the copy of
the Product-Management repository.

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

Make changes on development branch
----------------------------------

After performing the development work, commit the change to a development
branch. The commit message should describe the reason behind the change and any
necessary information to setup and use the change.

Run tests
---------

Run existing tests and add new tests, if needed. See Testing_ for detailed
instructions. Commit any changes to the development branch.


Push the change
---------------

The development branch needs be pushed to the personal fork and OSEHRA repository.

To Personal Fork
+++++++++++++++++

In this example, the branch "add_new_file" will be pushed to the personal fork
generated earlier.

.. parsed-literal::

  softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)
  $ git push personal add_new_file
  
This branch will be not be used right away, but will be used when the change is
submitted to the ``master`` branch

To OSEHRA-Sandbox
++++++++++++++++++

Prior to submitting it to the OSEHRA repository, the development work should be
merged with the vivian-demo branch. This should be done locally to allow for
any conflicts to be determined prior to the push. The merge command takes only
one argument, which is the name of the branch to be merged, and should be
performed on the target branch. The example below checks out the 
``vivian-demo`` branch and then merges the commits from add_new_file

.. parsed-literal::

  softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)
    $ git checkout vivian-demo
    Switched to branch 'vivian-demo'
    Your branch is up-to-date with 'origin/vivian-demo'.

    softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)

    $ git merge add_new_file

Once the merge is completed and any conflicts have been resolved, push the
vivian-demo branch to the OSEHRA-Sandbox Repository.

.. parsed-literal::

  softhat@softhatvm /d/wamp/www/Product-Management/Visual (master)
  $git push origin vivian-demo

After pushing to the vivian-demo branch, send an email to the
Visualization Open Source Project Group (visualization@groups.osehra.org )
to request that the vivian-demo instance be updated.

Making additional changes
++++++++++++++++++++++++++

If more changes are necessary, due to suggestions or bug reports, the above
cycle should be repeated, being sure to keep the separate development branch
up-to-date for the eventual merge to master.

Submitting to master
---------------------

At the end of the sprint, all the approved updates need to be moved from the
vivian-demo branch to the master branch. This process is going to be completed
through the use of pull requests on the Github website. The method for
submitting a pull request is fairly straightforward. Once the branch or commit
has been pushed to a personal fork, click on the "New pull request" button
found on the webpage of the user’s fork.

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
generate the request. Below is an example using a different branch which shows
both the places where the information is set and the button to generate the
pull request.

.. figure::
   http://code.osehra.org/content/named/SHA1/c56600-vivianCreatePullRequestHighlight.png
   :align: center
   :alt:  Github page of forked repository with connection information highlighted


When the pull request has been submitted, this change will then be reviewed by
the members of the OSEHRA Visualization Open Source Project Group. At this
point, the members of the project group, owners of the repository, and general
public can leave comments and suggestions for improvement.

Now, there are two potential paths for the submission:

Pull request approved
++++++++++++++++++++++

If the submission has been approved, the pull request will be accepted and the
changes will be merged into the master branch. This master branch will then be
updated on the display instance using that branch, found at
http://code.osehra.org/vivian/.

Changes requested
+++++++++++++++++

If a fix or further work is requested, the submitter should return to the
development environment.  Once the updates are completed, the submission should
re-enter the process. Any changes made should be made on the same development
branch as the original one. As long as the pull request is open, any commits
pushed to the pull request branch on Github will be automatically included as
part of the pull request.

If major functionality is being changed or a large rewrite is necessary, it is
recommended that the submission passes through the vivian-demo process before
returning to update the pull request.

Minor or cosmetic changes simply be made to to the same branch and pushed to
the remote as before.


.. _Testing: testing.rst
