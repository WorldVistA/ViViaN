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
import argparse
import os
import re
import time
import unittest

class test_links(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.quit()

  def go_to_interface_link(self, link_id, destination):
    global driver
    global webroot
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id(link_id).click()
    time.sleep(1)
    expected_url = os.path.join(webroot, destination)
    expected_url = os.path.normpath(expected_url.replace("http://", "https://"))
    current_url = os.path.normpath(driver.current_url)
    self.assertEqual(current_url, expected_url)
    driver.back()
    time.sleep(1)

  # Join the Visualization Working Group
  def test_01_visualization_working_group(self):
    global driver
    nav_button = driver.find_element_by_xpath('//*[@id="navigation_buttons"]/nav/div/ul[2]/li[2]/a')
    nav_button.click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'https://www.osehra.org/content/visualization-open-source-project-group')
    driver.back()
    time.sleep(1)

  # VA Visualizations
  def test_02_business_information_model(self):
    global driver
    driver.find_element_by_id('va-visualizations').click()
    driver.find_element_by_id('business-information-model').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'https://bim.osehra.org/')
    driver.back()
    time.sleep(1)

  def test_03_hybrid_information_model(self):
    global driver
    driver.find_element_by_id('va-visualizations').click()
    driver.find_element_by_id('hybrid-information-model').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'https://him.osehra.org/')
    driver.back()
    time.sleep(1)

  # VistA Interfaces
  def test_04_all_hl7(self):
    self.go_to_interface_link('all_hl7', 'files/101/All-HL7.html')

  def test_05_all_hlo(self):
    self.go_to_interface_link('all_hlo', 'files/779_2/All-HLO.html')

  def test_06_all_icr(self):
    self.go_to_interface_link('all_icr', 'files/ICR/All-ICR%20List.html')

  def test_07_all_protocols(self):
    self.go_to_interface_link('all_protocols', 'files/101/All-Protocols.html')

  def test_08_all_rpc(self):
    self.go_to_interface_link('all_rpc', 'files/8994/All-RPC.html')

if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="Test all links on navigation bar")
  parser.add_argument("-r", dest='webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  parser.add_argument("-b", dest='browser', default="FireFox", required=False, help="Web browser to use for testing [FireFox, Chrome]")
  result = vars(parser.parse_args())
  if result['browser'].upper() == "CHROME":
    driver = webdriver.Chrome()
  else:
    driver = webdriver.Firefox()
  webroot = result['webroot']
  driver.get(webroot + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_links)
  unittest.TextTestRunner(verbosity=2).run(suite)
