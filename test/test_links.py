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
from selenium import webdriver
from selenium.webdriver.support.ui import Select
from vivian_test_utils import setup_webdriver

import re
import time
import unittest

class test_links(unittest.TestCase):
  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def go_to_interface_link(self, link_id, destination):
    self.go_to_files_link('vista-interfaces', link_id, destination)

  def go_to_files_link(self, menu_id, link_id, destination):
    expected_url = webroot + "/vivian-data/" + destination
    self.go_to_link(menu_id, link_id, expected_url)

    driver.back()
    time.sleep(1)

  def go_to_vivian_link(self, menu_id, link_id, destination):
    expected_url = webroot + "/vivian/" + destination
    self.go_to_link(menu_id, link_id, expected_url)

  def go_to_link(self, menu_id, link_id, expected_url):
    driver.find_element_by_id(menu_id).click()
    if link_id is not None:
      driver.find_element_by_id(link_id).click()
    time.sleep(1)
    self.assertEqual(driver.current_url, expected_url)

  # Skip ViViaN button (index.php) for now

  def test_01_protocol_menu(self):
    self.go_to_vivian_link('vista-menus', 'protocol_menu', 'vista_menus.php#101')

  def test_02_option_menu(self):
    self.go_to_vivian_link('vista-menus', 'option_menu', 'vista_menus.php#19')

  def test_03_bff_demo(self):
    self.go_to_vivian_link('bff-demo', None, 'bff_demo.php')

  def test_04_package_dependency(self):
    self.go_to_vivian_link('package-dependency', 'circle-layout', 'vista_pkg_dep.php')

  def test_05_package_dependency_chart(self):
    self.go_to_vivian_link('package-dependency', 'bar-chart', 'vista_pkg_dep_chart.php')

  def test_06_package_dependency_graph(self):
    self.go_to_vivian_link('package-dependency', 'force-directed-graph', 'package_dep_graph.php')

  def test_07_install_timeline(self):
    self.go_to_vivian_link('vista-install', 'install-timeline', 'installScale.php')

  def test_08_install_tree(self):
    self.go_to_vivian_link('vista-install', 'install-tree', 'patchDependency.php')

  # Now that we've navigated away from the main page, click on 'ViViaN' button
  def test_16_vivian(self):
    self.go_to_vivian_link('vivian', None, 'index.php')

  # VistA Interfaces
  def test_09_all_hl7(self):
    self.go_to_interface_link('all_hl7', '101/All-HL7.html')

  def test_10_all_hlo(self):
    self.go_to_interface_link('all_hlo', '779_2/All-HLO.html')

  def test_11_all_icr(self):
    self.go_to_interface_link('all_icr', 'ICR/All-ICR%20List.html')

  def test_12_all_protocols(self):
    self.go_to_interface_link('all_protocols', '101/All-Protocols.html')

  def test_13_all_rpc(self):
    self.go_to_interface_link('all_rpc', '8994/All-RPC.html')

  def test_14_all_name(self):
    self.go_to_files_link('vista-information', 'all_name', 'Namespace/Namespace.html')

  def test_15_all_number(self):
    self.go_to_files_link('vista-information', 'all_number', 'Numberspace/Numberspace.html')

  def test_16_query_vis(self):
    self.go_to_vivian_link('queryVis_stats', None, 'queryVis_stats.php')
    driver.back()
    time.sleep(1)

  # 'About' is tested in 'test_about'

  # Join the Visualization Working Group
  def test_17_visualization_working_group(self):
    driver.find_element_by_id("workinggroup").click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'https://www.osehra.org/content/visualization-open-source-project-group')
    driver.back()
    time.sleep(1)

  # FOIA VistA
  def test_18_vxvista(self):
    self.go_to_vivian_link('foia-vista', 'vxvista', 'vxvista/')
    driver.back()
    time.sleep(1)

  # VA Visualizations
  def test_19_business_information_model(self):
    self.go_to_link('va-visualizations', 'business-information-model', 'https://bim.osehra.org/')
    driver.back()
    time.sleep(1)

  def test_20_hybrid_information_model(self):
    self.go_to_link('va-visualizations', 'hybrid-information-model', 'https://him.osehra.org/')
    driver.back()
    time.sleep(1)

if __name__ == '__main__':
  description = "Test all links on navigation bar"
  page = "vivian/index.php"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_links)
  unittest.TextTestRunner(verbosity=2).run(suite)
