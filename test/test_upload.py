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
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.ui import Select
from vivian_test_utils import setup_webdriver
import argparse
import os
import re
import time
import unittest

class test_upload(unittest.TestCase):

  def close_modal_dialog(self):
    try:
      modal_title = driver.find_element_by_class_name('ui-dialog-titlebar')
      modal_title.find_element_by_tag_name("button").click()
    except:
      pass

  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def test_01_selectFile(self):
    time.sleep(1)
    select = driver.find_element_by_id('vivSelect')
    select.click()
    select.find_elements_by_tag_name("option")[3].click()
    select = driver.find_element_by_id('attributeSelect')
    select.click()
    select.find_elements_by_tag_name("option")[1].click()
    time.sleep(5)
    self.assertNotEqual(len(driver.find_elements_by_class_name('slice')), 0)
    ActionChains(driver).move_to_element(driver.find_element_by_class_name('slice')).perform()
    modal_title = driver.find_element_by_id('toolTip').find_element_by_id('header1')
    self.assertTrue(re.search("ABBREVIATION:", modal_title.text))

  def test_02_modalWindow(self):
    self.addCleanup(self.close_modal_dialog)
    time.sleep(1)
    select = driver.find_element_by_id('vivSelect')
    select.click()
    select.find_elements_by_tag_name("option")[3].click()
    select = driver.find_element_by_id('attributeSelect')
    select.click()
    select.find_elements_by_tag_name("option")[4].click()
    time.sleep(1)
    driver.find_element_by_class_name('slice').click()
    modal_title = driver.find_element_by_class_name('ui-dialog-title')
    for obj in driver.find_elements_by_class_name("ui-accordion-header-icon"):
      obj.click()
    self.assertTrue(re.search("Filtered Object Information", modal_title.text))
    
  def test_03_showTable(self):
    time.sleep(1)
    driver.find_element_by_id('toggleDisplay').click()
    self.assertTrue(driver.find_element_by_id('tables_placeholder_wrapper').is_displayed())
  
  def test_04_tableWork(self):
    searchBox = driver.find_element_by_id('tables_placeholder_filter').find_element_by_tag_name('input').send_keys("black");
    self.assertTrue(re.search("3 entries", driver.find_element_by_id('tables_placeholder_info').text))
    for rowBox in driver.find_elements_by_class_name("sorting"):
      rowBox.click()
    time.sleep(1)
  
if __name__ == '__main__':
  description="Test the upload_vis page of the ViViaN(TM) tool, the VistA Package visualization"
  page = "vivian/queryVis_stats.php"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_upload)
  unittest.TextTestRunner(verbosity=2).run(suite)
