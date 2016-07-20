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
import argparse
import unittest
import re
import time
import selenium_utils as Utils

class test_menus(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

  def test_01_reset(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_resetAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertEqual(oldSize, newSize)

  def test_02_collapse_all(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize > newSize)
    self.assertEqual(newSize, 1)

  def test_03_legend(self):
    global driver
    color_options = ["#E0E0E0",'']
    legend_list = driver.find_elements_by_class_name('legend')
    for item in legend_list[1:]:
      item.click()
      color_options[1]=item.find_element_by_tag_name('text').get_attribute("fill")
      node_list = driver.find_elements_by_class_name('node')
      for node in node_list:
        node_fill = node.find_element_by_tag_name('text').get_attribute("fill")
        self.assertTrue(node_fill in color_options )
      time.sleep(1)

  def test_04_menu_autocomplete(self):
    global driver
    target_menu_text = "Core Applications"
    ac_form = driver.find_element_by_id("autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      if(re.search(target_menu_text, option.text)):
        option.click()
        break
    time.sleep(1)
    node_list = driver.find_elements_by_class_name('node')
    self.assertEqual(node_list[-1].text, target_menu_text)

  def test_05_option_autocomplete(self):
    global driver
    target_menu_text = "Monitor Taskman"
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      if(re.search(target_menu_text,option.text)):
        option.click()
        break
    time.sleep(1)
    # Compare images to match paths
    driver.save_screenshot("path_image_pass_new.png")
    self.assertTrue(Utils.compareImg("path_image_pass"))

if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/vista_menus.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_menus)
  unittest.TextTestRunner(verbosity=2).run(suite)