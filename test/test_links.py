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
import unittest
import re
import time

class test_links(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

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
    self.assertEqual(driver.current_url, 'http://bim.osehra.org/')
    driver.back()
    time.sleep(1)

  def test_03_hybrid_information_model(self):
    global driver
    driver.find_element_by_id('va-visualizations').click()
    driver.find_element_by_id('hybrid-information-model').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://him.osehra.org/')
    driver.back()
    time.sleep(1)

  # VistA Interfaces
  def test_04_all_hl7(self):
    global driver
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id('all_hl7').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://code.osehra.org/vivian/files/101/All-HL7.html')
    driver.back()
    time.sleep(1)

  def test_05_all_hlo(self):
    global driver
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id('all_hlo').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://code.osehra.org/vivian/files/779_2/All-HLO.html')
    driver.back()
    time.sleep(1)

  def test_06_all_icr(self):
    global driver
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id('all_icr').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://code.osehra.org/vivian/files/ICR/All-ICR%20List.html')
    driver.back()
    time.sleep(1)

  def test_07_all_protocols(self):
    global driver
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id('all_protocols').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://code.osehra.org/vivian/files/101/All-Protocols.html')
    driver.back()
    time.sleep(1)

  def test_08_all_rpc(self):
    global driver
    driver.find_element_by_id('vista-interfaces').click()
    driver.find_element_by_id('all_rpc').click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'http://code.osehra.org/vivian/files/8994/All-RPC.html')
    driver.back()
    time.sleep(1)

if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="Access the 'About' Text of the ViViaN(TM) webpage")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_links)
  unittest.TextTestRunner(verbosity=2).run(suite)
