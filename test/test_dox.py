#---------------------------------------------------------------------------
# Copyright 2019 The Open Source Electronic Health Record Alliance
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

from selenium import webdriver
from selenium.webdriver.support.ui import Select
from vivian_test_utils import setup_webdriver

import re
import time
import unittest

class DoxTestCase(unittest.TestCase):
  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def _get_dox_url(self, page):
    return webroot + "/dox/" + page

  def go_to_dox_page(self, page):
    driver.get(self._get_dox_url(page))
    self.assertFalse("not found" in driver.page_source)

  def go_to_index_page(self, id, expected_url):
    driver.find_element_by_id(id).click()
    time.sleep(1)
    expected_url = self._get_dox_url(expected_url)
    self.assertEqual(driver.current_url, expected_url)

  # ---------------------------------------------------------------------------
  # Index pages and navigation buttons
  # ---------------------------------------------------------------------------

  # Skip Home (index.html) for now
  def test_01_package_list(self):
    self.go_to_index_page('package_list', 'packages.html')

  def test_02_routine_list(self):
    self.go_to_index_page('routine_list', 'routines.html')

  def test_03_global_list(self):
    self.go_to_index_page('global_list', 'globals.html')

  def test_04_fileman_files_list(self):
    self.go_to_index_page('fileman_files_list', 'filemanfiles.html')

  def test_05_fileman_subfiles_list(self):
    self.go_to_index_page('fileman_subfiles_list', 'filemansubfiles.html')

  def test_06_package_component_list(self):
    self.go_to_index_page('package_component_list', 'PackageComponents.html')

  def test_07_package_namespace_mapping(self):
    self.go_to_index_page('package_namespace_mapping', 'Packages_Namespace_Mapping.html')

  # Now that we've navigated away from the main page, click on 'Home' button
  def test_08_home(self):
    self.go_to_index_page('home', 'index.html')

  # ---------------------------------------------------------------------------
  # Packages
  # ---------------------------------------------------------------------------

  def test_09_package_lexicon_utility(self):
    self.go_to_dox_page("Package_Lexicon_Utility.html")

  def test_10_package_va_fileman(self):
    self.go_to_dox_page("Package_VA_FileMan.html")

  # ---------------------------------------------------------------------------
  # Globals
  # ---------------------------------------------------------------------------

  # ^GMRD(120.57
  def test_11_global(self):
    self.go_to_dox_page("Global_XkdNUkQoMTIwLjU3.html")

  # ^MDC(704.1161 (non-fileman)
  def test_12_global(self):
    self.go_to_dox_page("Global_Xk1EQyg3MDQuMTE2MQ==.html")

  # ^USR(8930.8
  def test_13_global(self):
    self.go_to_dox_page("Global_XlVTUig4OTMwLjg=.html")

  # ^ONCO(160 (ICR)
  def test_14_global(self):
    self.go_to_dox_page("Global_Xk9OQ08oMTYw.html")

  # ---------------------------------------------------------------------------
  # Routines
  # ---------------------------------------------------------------------------

  def test_15_routine_MCOBGC(self):
    self.go_to_dox_page("Routine_MCOBGC.html")

  def test_16_routine_XUOAAHL7(self):
    self.go_to_dox_page("Routine_XUOAAHL7.html")

  def test_17_routine_MCARAMLH(self):
    self.go_to_dox_page("Routine_MCARAMLH.html")

  def test_18_routine_MCARAMLG(self):
    self.go_to_dox_page("Routine_MCARAMLG.html")

  # Entry points
  def test_19_routine_SDAM1(self):
    self.go_to_dox_page("Routine_SDAM1.html")

  # Interaction
  def test_20_routine_RGUTFTP(self):
    self.go_to_dox_page("Routine_RGUTFTP.html")

  # Information
  def test_21_routine_MMRSIPC5(self):
    self.go_to_dox_page("Routine_MMRSIPC5.html")

  # Entry Points - DBIA/ICR
  def test_22_routine_MMRSIPC4(self):
    self.go_to_dox_page("Routine_MMRSIPC4.html")

  def test_23_routine_PSX550(self):
    self.go_to_dox_page("Routine_PSX550.html")

  # Platform Dependent Routine
  def test_24_platform_dependent_routine(self):
    self.go_to_dox_page("Routine_%25ZOSV1.html")

  # Source Code
  def test_25_source_code(self):
    self.go_to_dox_page("Routine_PSX550_source.html")

  # ---------------------------------------------------------------------------
  # Subfile
  # ---------------------------------------------------------------------------

  def test_26_subfile(self):
    self.go_to_dox_page("SubFile_.111.html")

  # ---------------------------------------------------------------------------
  # Package Component
  # ---------------------------------------------------------------------------

  def test_27_remote_procedure(self):
    self.go_to_dox_page("Remote_Procedure_MAGQBP_PARM.html")

if __name__ == '__main__':
  description = "Test DOX pages"
  page = "dox"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(DoxTestCase)
  unittest.TextTestRunner(verbosity=2).run(suite)
