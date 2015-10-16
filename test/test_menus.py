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
import selenium_utils as Utils

class test_menus(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

  # def test_09_node(self):
  # #  # ********************************************
  # #  #TODO: Find a way to click on circle for collapsing of a node
  # #  # ********************************************
    # global driver
    # driver.find_element_by_link_text("VHA BFF Demo").click()
    # driver.find_element_by_link_text("VistA Menus").click()
    # nodes = driver.find_elements_by_class_name('node')
    # print len(nodes)
  # #  oldSize = len(nodes)
  # #  print nodes
  # #  print nodes[-1].text
  # #  print nodes[0].text

    # #time.sleep(30)
    # #for node in nodes:

    # ActionChains(driver).move_to_element(nodes[-1].find_element_by_tag_name("circle")).double_click(nodes[-1]).perform()
    # time.sleep(5)
    # nodes = driver.find_elements_by_class_name('node')
    # print len(nodes)
    #print "*********************"
    #print oldSize
    #print newSize
    #print "*********************"
    #self.assertTrue(newSize != oldSize)

  def test_01_reset(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_resetAllNode')]")
    button.click()
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize == newSize)

  def test_collapse(self):
    global driver
    oldSize = len(driver.find_elements_by_class_name('node'))
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_collapseAllNode')]")
    button.click()
    time.sleep(1)
    newSize = len(driver.find_elements_by_class_name('node'))
    self.assertTrue(oldSize > newSize)

  def test_04_autocomplete(self):
    target_menu_text = "Core Applications"
    global driver
    ac_form = driver.find_element_by_id("autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      ac_option = option.find_element_by_tag_name('a')
      if(re.search(target_menu_text,ac_option.text)):
        ac_option.click()
    time.sleep(1)
    node_list = driver.find_elements_by_class_name('node')
    self.assertTrue(node_list[-1].text == target_menu_text)

  def test_05_menuAutoComplete(self):
    target_menu_text = "Monitor Taskman"
    global driver
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      ac_option = option.find_element_by_tag_name('a')
      if(re.search(target_menu_text,ac_option.text)):
        ac_option.click()
    time.sleep(1)
    # Now being to compare images to match paths
    driver.save_screenshot("path_image_pass_new.png")
    self.assertTrue(Utils.compareImg("path_image_pass"))

  def test_06_menuAutoCompleteFail(self):
    target_menu_text = "Problem Device report"
    global driver
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      ac_option = option.find_element_by_tag_name('a')
      if(re.search(target_menu_text,ac_option.text)):
        ac_option.click()
    time.sleep(1)
    # Now being to compare images to match paths
    driver.save_screenshot("path_image_fail_new.png")
    self.assertFalse(Utils.compareImg("path_image_fail"))

  def test_07_menuAutoCompleteFail2(self):
    target_menu_text = "Process Insurance Buffer"
    global driver
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      ac_option = option.find_element_by_tag_name('a')
      if(re.search(target_menu_text,ac_option.text)):
        ac_option.click()
    time.sleep(1)
    # Now being to compare images to match paths
    driver.save_screenshot("path_image_fail2_new.png")
    self.assertFalse(Utils.compareImg("path_image_fail2"))


  def test_03_legend(self):
    color_options = ["#E0E0E0",'']
    global driver
    legend_list = driver.find_elements_by_class_name('legend')
    for item in legend_list[1:]:
      item.click()
      color_options[1]=item.find_element_by_tag_name('text').get_attribute("fill")
      node_list = driver.find_elements_by_class_name('node')
      for node in node_list:
        node_fill = node.find_element_by_tag_name('text').get_attribute("fill")
        self.assertTrue(node_fill in color_options )
      time.sleep(1)

if __name__ == '__main__':
  parser =argparse.ArgumentParser(description="")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViAN instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/vista_menus.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_menus)
  unittest.TextTestRunner(verbosity=2).run(suite)