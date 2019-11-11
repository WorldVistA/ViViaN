#---------------------------------------------------------------------------
# Copyright 2015 The Open Source Electronic Health Record Alliance
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#---------------------------------------------------------------------------
cmake_minimum_required(VERSION 2.8.0)

project(ViViaN-Test, NONE)
enable_testing()

file(GLOB VIVIAN_TEST_FILES ${CMAKE_CURRENT_SOURCE_DIR}/test*.py)

#message(STATUS "The Image comparison part of the Menus test utilizes the PILLOW Python Library.
#Installation instructions for PILLOW are found here:
#    http://pillow.readthedocs.org/en/3.0.x/installation.html
#")

if(NOT DEFINED PYTHON_EXECUTABLE)
  include(FindPythonInterp)
endif(NOT DEFINED PYTHON_EXECUTABLE)

set(WEB_ROOT "https://code.osehra.org" CACHE STRING "Set web root of pages to test")
set(BROWSER "FireFox" CACHE STRING "Web browser to use for testing")
set_property(CACHE BROWSER PROPERTY STRINGS FireFox Chrome)
foreach(test_file ${VIVIAN_TEST_FILES})
  get_filename_component(test_name ${test_file} NAME_WE)
  add_test("VIVIAN_${test_name}" "${PYTHON_EXECUTABLE}" "${test_file}" "-r" "${WEB_ROOT}" "-b" "${BROWSER}")
  set_tests_properties("VIVIAN_${test_name}" PROPERTIES FAIL_REGULAR_EXPRESSION "FAIL")
endforeach()
