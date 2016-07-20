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

  def test_01_visualization_working_group(self):
    global driver
    nav_button = driver.find_element_by_xpath('//*[@id="navigation_buttons"]/nav/div/ul[2]/li[2]/a')
    nav_button.click()
    time.sleep(1)
    self.assertEqual(driver.current_url, 'https://www.osehra.org/content/visualization-open-source-project-group')
    driver.back()
    time.sleep(1)

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

if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="Access the 'About' Text of the ViViaN(TM) webpage")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_links)
  unittest.TextTestRunner(verbosity=2).run(suite)
