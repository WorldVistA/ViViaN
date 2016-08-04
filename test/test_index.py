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
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertEqual(oldSize, newSize)

  def test_02_expand_all(self):
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
    self.assertTrue(re.search("Visualizing VistA and Namespace", title.text))

  def test_04_legend(self):
    global driver
    legend = driver.find_elements_by_class_name('legend')
    prev = ''
    titles = ['All', 'OSEHRA VistA', 'VA FOIA VistA', 'DSS vxVistA']
    for idx, entry in enumerate(legend):
      self.assertEqual(entry.find_element_by_tag_name('text').text, titles[idx])
      entry.click()
      nodes = driver.find_elements_by_class_name('node')
      # find a leaf node
      for node in nodes:
        node_path = node.find_element_by_tag_name('path')
        if node_path.get_attribute('name') == 'circle':
          node_text = node.find_element_by_tag_name('text')
          break;
      self.assertNotEqual(node_text.get_attribute("fill"), prev, 'Expected color to change for node "' + node_text.text + '"')
      prev = node_text.get_attribute("fill")

  def test_05_modal_title(self):
    global driver

    self.addCleanup(self.close_modal_dialog)

    nodes = driver.find_elements_by_class_name('node')
    # find a leaf node
    for node in nodes:
      node_path = node.find_element_by_tag_name('path')
      if node_path.get_attribute('name') == 'circle':
        node_text = node.find_element_by_tag_name('text')
        break;
    # open dialog
    node_text.click()
    modal_title = driver.find_element_by_class_name('ui-dialog-title')
    self.assertTrue(re.search(node_text.text, modal_title.text))

  def _test_06_modal_accordion(self):
    global driver

    self.addCleanup(self.close_modal_dialog)

    # find 'Barcode Medication Administration' node
    nodes = driver.find_elements_by_class_name('node')
    for node in nodes:
      node_text = node.find_element_by_tag_name('text')
      if node_text.text == 'Barcode Medication Administration':
        break;

    # Open modal dialog
    node_text.click()

    accordion = driver.find_element_by_id("accordion")
    modalCategories = ['namespaces','dependencies','interface','himInfo', 'description']
    modalRegex = ['Includes.+Excludes','Dependencies \&amp; Code View','M API.+Web Service API',
                  'HIM Visualization for','[A-Za-z &/]+']
    for i in range(1,6): # 1, 2, 3, 4, 5
      # Test accordion selection of each header
      modal_accordion = accordion.find_element_by_id('ui-id-'+str(i))
      modal_accordion.click()
      self.assertTrue(modal_accordion.get_attribute('aria-selected'))
      # Test accordion content
      content = accordion.find_element_by_id(modalCategories[i-1]).get_attribute('innerHTML');
      self.assertTrue(re.search(modalRegex[i-1], content),
                      'Looking for "' + modalRegex[i-1] + '" Found "' + content + '" for node "' + node_text.text + '"')

  def close_modal_dialog(self):
    global driver
    modal_title = driver.find_element_by_class_name('ui-dialog-titlebar')
    modal_title.find_element_by_tag_name("button").click()


  def test_07_expand_collapse_nodes(self):
    global driver
    nodes = driver.find_elements_by_class_name('node')
    oldSize = len(nodes)
    # Click on node to collapse or expand some nodes
    nodes[-1].find_element_by_tag_name("path").click()
    time.sleep(1)
    nodes = driver.find_elements_by_class_name('node')
    self.assertNotEqual(len(nodes), oldSize)
    # Click on the node again
    nodes[-1].find_element_by_tag_name("path").click()
    time.sleep(1)
    # Should be back where we started
    nodes = driver.find_elements_by_class_name('node')
    self.assertEqual(len(nodes), oldSize)

  def test_08_collapse_all(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize > newSize)
    self.assertEqual(newSize, 1)



if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="Test the index page of the ViViaN(TM) tool, the VistA Package visualization")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_index)
  unittest.TextTestRunner(verbosity=2).run(suite)