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
file(GLOB VIVIAN_TEST_FILES ${CMAKE_CURRENT_SOURCE_DIR}/test*.py)
message(STATUS "The Image comparison part of the Menus test utilizes the PILLOW Python Library.
Installation instructions for PILLOW are found here:
    http://pillow.readthedocs.org/en/3.0.x/installation.html
")

set(VIVIAN_WEB_ROOT "http://code.osehra.org/vivian" CACHE STRING "Set web root of vivian page to test")
foreach(test_file ${VIVIAN_TEST_FILES})
  get_filename_component(test_name ${test_file} NAME_WE)
  add_test("VIVIAN_${test_name}" "${PYTHON_EXECUTABLE}" "${test_file}" "-r" "${VIVIAN_WEB_ROOT}")
  set_tests_properties("VIVIAN_${test_name}" PROPERTIES FAIL_REGULAR_EXPRESSION "FAIL:")
endforeach()
