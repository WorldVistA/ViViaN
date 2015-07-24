#.rst:
# CTestCoverageCollectGCOV
# ------------------------
#
# This module provides the function ``ctest_coverage_collect_gcov``.
# The function will run gcov on the .gcda files in a binary tree and then
# package all of the .gcov files into a tar file with a data.json that
# contains the source and build directories for CDash to use in parsing
# the coverage data. In addtion the Labels.json files for targets that
# have coverage information are also put in the tar file for CDash to
# asign the correct labels. This file can be sent to a CDash server for
# display with the
# :command:`ctest_submit(CDASH_UPLOAD)` command.
#
# .. command:: cdash_coverage_collect_gcov
#
#   ::
#
#     ctest_coverage_collect_gcov(TARBALL <tarfile>
#       [SOURCE <source_dir>][BUILD <build_dir>]
#       [GCOV_COMMAND <gcov_command>]
#       [GCOV_OPTIONS <options>...]
#       )
#
#   Run gcov and package a tar file for CDash.  The options are:
#
#   ``TARBALL <tarfile>``
#     Specify the location of the ``.tar`` file to be created for later
#     upload to CDash.  Relative paths will be interpreted with respect
#     to the top-level build directory.
#
#   ``SOURCE <source_dir>``
#     Specify the top-level source directory for the build.
#     Default is the value of :variable:`CTEST_SOURCE_DIRECTORY`.
#
#   ``BUILD <build_dir>``
#     Specify the top-level build directory for the build.
#     Default is the value of :variable:`CTEST_BINARY_DIRECTORY`.
#
#   ``GCOV_COMMAND <gcov_command>``
#     Specify the full path to the ``gcov`` command on the machine.
#     Default is the value of :variable:`CTEST_COVERAGE_COMMAND`.
#
#   ``GCOV_OPTIONS <options>...``
#     Specify options to be passed to gcov.  The ``gcov`` command
#     is run as ``gcov <options>... -o <gcov-dir> <file>.gcda``.
#     If not specified, the default option is just ``-b``.
#
#   ``QUIET``
#     Suppress non-error messages that otherwise would have been
#     printed out by this function.

#=============================================================================
# Copyright 2014-2015 Kitware, Inc.
#
# Distributed under the OSI-approved BSD License (the "License");
# see accompanying file Copyright.txt for details.
#
# This software is distributed WITHOUT ANY WARRANTY; without even the
# implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the License for more information.
#=============================================================================
# (To distribute this file outside of CMake, substitute the full
#  License text for the above reference.)
include(CMakeParseArguments)
function(ctest_coverage_collect_jscover)
set(options QUIET)
set(oneValueArgs TARBALL SOURCE BUILD)
set(multiValueArgs target_dirs)

cmake_parse_arguments(JSCover  "${options}" "${oneValueArgs}"
"${multiValueArgs}" "" ${ARGN} )
if(NOT DEFINED JSCover_TARBALL)
  message(FATAL_ERROR
  "TARBALL must be specified. for ctest_coverage_collect_gcov")
endif()
if(NOT DEFINED JSCover_SOURCE)
  set(source_dir "${CTEST_SOURCE_DIRECTORY}")
else()
  set(source_dir "${JSCover_SOURCE}")
endif()
if(NOT DEFINED JSCover_BUILD)
  set(binary_dir "${CTEST_BINARY_DIRECTORY}")
else()
  set(binary_dir "${JSCover_BUILD}")
endif()

# collect the .json files saved from the webpage
set(JSCover_files)
file(GLOB_RECURSE jsonfiles "${binary_dir}/*.json") #RELATIVE ${binary_dir} 
list(LENGTH jsonfiles len)
if(${len} GREATER 0)
  list(APPEND JSCover_files ${jsonfiles})
endif()

# return early if no coverage files were found
list(LENGTH JSCover_files len)
if(len EQUAL 0)
  if (NOT JSCover_QUIET)
    message("ctest_coverage_collect_JSCover: No .json files found, "
    "ignoring coverage request.")
  endif()
  return()
endif()

# tar up the coverage info with the same date so that the md5
# sum will be the same for the tar file independent of file time
# stamps
string(REPLACE ";" "\r\n" JSCover_files "${JSCover_files}")
#string(REPLACE "(" "\(" JSCover_files "${JSCover_files}")
#string(REPLACE ")" "\)" JSCover_files "${JSCover_files}")

file(WRITE "${binary_dir}/coverage_file_list.txt"
"${JSCover_files}
")

if (JSCover_QUIET)
set(tar_opts "cfj")
else()
set(tar_opts "cvfj")
endif()
execute_process(COMMAND
${CMAKE_COMMAND} -E tar ${tar_opts} ${JSCover_TARBALL}
    "--format=gnutar"
    "--files-from=${binary_dir}/coverage_file_list.txt"
WORKING_DIRECTORY ${binary_dir})
endfunction()
