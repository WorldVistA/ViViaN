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
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
import argparse
import unittest
import re
import time

class test_index(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

  def test_01_reset(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_expandAllNode')]")
    button.click()
    time.sleep(1)
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_resetAllNode')]")
    button.click()
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize == newSize)

  def test_02_expand(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_expandAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize < newSize)

  def test_03_title(self):
    global driver
    title = driver.find_element_by_id("title")
    self.assertTrue(re.search("Visualizing VistA And Namespace", title.text))

  def test_04_legend(self):
    global driver
    legend = driver.find_elements_by_class_name('legend')
    prev = ''
    for entry in legend:
      entry.click()
      nodes = driver.find_elements_by_class_name('node')
      node_text = nodes[40].find_element_by_tag_name('text')
      self.assertTrue(not(node_text.get_attribute("fill") == prev))
      prev = node_text.get_attribute("fill")

  def test_05_modal_title(self):
    global driver
    nodes = driver.find_elements_by_class_name('node')
    node_text = nodes[40].find_element_by_tag_name('text')
    node_text.click()
    modal_title = driver.find_element_by_id('ui-id-1')
    self.assertTrue(re.search(node_text.text, modal_title.text))

  def test_06_modal_accordion(self):
    modalCategories = ['namespaces','dependencies','interface','description']
    modalRegex = ['Includes.+Excludes','Dependencies \& Code View','M API.+Remote Procedure Call','[A-Za-z &/]+']
    global driver
    nodes = driver.find_elements_by_class_name('node')
    node_text = nodes[40].find_element_by_tag_name('text')
    accordion = driver.find_element_by_id("accordion")
    node_text.click()
    for i in range(4):
      # Test accordion selection of each header
      modal_accordion = accordion.find_element_by_id('ui-accordion-accordion-header-'+str(i))
      modal_accordion.click()
      self.assertTrue(modal_accordion.get_attribute('aria-selected'))

    # Test accordion content
      content = accordion.find_element_by_id(modalCategories[1]).get_attribute('innerHTML');
      self.assertTrue(modalRegex[i],content)
    # Close modal window
    modal_title = driver.find_element_by_class_name('ui-dialog-titlebar')
    modal_title.find_element_by_tag_name("button").click()

  def test_07_node(self):
    global driver
    nodes = driver.find_elements_by_class_name('node')
    oldSize = len(nodes)
    ActionChains(driver).move_to_element(nodes[0].find_element_by_tag_name("circle")).click(nodes[0]).perform()
    time.sleep(2)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(newSize <> oldSize)

  def test_collapse(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize > newSize)



if __name__ == '__main__':
  parser =argparse.ArgumentParser(description="Test the index page of the ViViaN(TM) tool, the VistA Package visualization")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_index)
  unittest.TextTestRunner(verbosity=2).run(suite)