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
from vivian_test_utils import setup_webdriver
import unittest
import re
import time
import selenium_utils as Utils

class test_menus(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.quit()

  def test_01_reset(self):
    global driver
    time.sleep(1)
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
      text_element = item.find_element_by_tag_name('text')
      # When using FireFox, need to click on text, not top-level element
      # May be related to:
      # https://github.com/mozilla/geckodriver/issues/653
      text_element.click()
      color_options[1] = text_element.get_attribute("fill")
      node_list = driver.find_elements_by_class_name('node')
      for node in node_list:
        node_fill = node.find_element_by_tag_name('text').get_attribute("fill")
        self.assertTrue(node_fill in color_options )
      time.sleep(1)

  def test_04_menu_autocomplete(self):
    global driver
    target_menu_text = "TimeKeeper Main Menu"
    ac_form = driver.find_element_by_id("autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_menu_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      if(re.search(target_menu_text, option.text)):
        option.click()
        break
    else:
      self.fail("Failed to find " + target_menu_text)
    time.sleep(2)
    node_list = driver.find_elements_by_class_name('node')
    self.assertEqual(node_list[0].text, target_menu_text)

  def test_05_option_autocomplete(self):
    global driver
    target_option_text = "XUTM ZTMON: [MTM]Monitor Taskman"
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys("monitor taskman")
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    option_names = []
    if len(ac_list) == 2:
      ac_list[1].location_once_scrolled_into_view
      ac_list[1].click()
    else:
      self.fail("Failed to find " + target_option_text)
    time.sleep(1)
    node_list = driver.find_elements_by_class_name('node')
    self.assertEqual(node_list[0].text, "Systems Manager Menu")

  def DISABLED_test_06_option_autocomplete_path(self):
    global driver
    driver.set_window_size(1024, 768)
    target_option_text = "Monitor Taskman"
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_option_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      if(re.search(target_option_text,option.text)):
        option.click()
        break
    time.sleep(1)
    # Compare images to match paths
    display = driver.find_element_by_id("treeview_placeholder").find_element_by_tag_name('svg')
    Utils.take_screenshot(driver,"path_image_pass_new.png", display)
    self.assertTrue(Utils.compareImg("path_image_pass"))

  def DISABLED_test_07_option_autocomplete_multipath(self):
    global driver
    driver.set_window_size(1024, 768)
    target_menu_text = "PSD PURCHASE ORDER REVIEW"
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
    display = driver.find_element_by_id("treeview_placeholder").find_element_by_tag_name('svg')
    Utils.take_screenshot(driver,"multi_path_image_pass_new.png", display)
    self.assertTrue(Utils.compareImg("multi_path_image_pass"))

  def test_08_option_autocomplete_menuDisplay(self):
    global driver
    target_option_text = "PSD PURCHASE ORDER REVIEW"
    target_option_menu_text = "Top Level Menu: Controlled Substances Menu"
    ac_form = driver.find_element_by_id("option_autocomplete")
    ac_form.clear()
    ac_form.send_keys(target_option_text)
    time.sleep(1)
    ac_list = driver.find_elements_by_class_name('ui-menu-item')
    for option in ac_list:
      if(re.search(target_option_text,option.text)):
        ActionChains(driver).move_to_element(option).perform()
        # Capture the new text of the highlight and compare it to expected
        self.assertEqual(option.text, target_option_menu_text,
          "Option was not found in same menu: Expected '"+ target_option_menu_text + "' Found: '" + option.text +"'" )
        break
    else:
      self.fail("Failed to find " + target_option_text)
    time.sleep(1)

  def test_09_panZoom(self):
    global driver

    global browser
    if browser == "FIREFOX":
      return # Test fails on FireFox, skip it for now

    # Check pan by dragging and dropping on display
    menuTree = driver.find_element_by_id("treeview_placeholder").find_element_by_tag_name('svg')
    menuTreeDisplay = menuTree.find_element_by_tag_name('g')
    oldTrans =  menuTreeDisplay.get_attribute("transform")
    ActionChains(driver).move_to_element(menuTree).drag_and_drop_by_offset(menuTree, 350, 200).perform()
    time.sleep(1)
    self.assertNotEqual(oldTrans, menuTreeDisplay.get_attribute("transform"),
                        "Transform was the same after attempting drag and drop")

    # Check zoom by double-clicking on display
    oldTrans = menuTreeDisplay.get_attribute("transform")
    ActionChains(driver).move_to_element_with_offset(menuTree, 10, 10).double_click(menuTreeDisplay).perform()
    time.sleep(1)
    self.assertNotEqual(oldTrans, menuTreeDisplay.get_attribute("transform"),
                        "Transform was the same after attempting to zoom")

  def test_10_panCenter(self):
    global driver

    menuTree = driver.find_element_by_id("treeview_placeholder").find_element_by_tag_name('svg')
    menuTreeDisplay = menuTree.find_element_by_tag_name('g')
    ActionChains(driver).move_to_element(menuTree).drag_and_drop_by_offset(menuTree, 300, 200).perform()
    oldVal = menuTreeDisplay.get_attribute("transform")
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_centerDisplay()')]")
    button.click()
    time.sleep(1)
    newVal = menuTreeDisplay.get_attribute("transform")
    self.assertNotEqual(oldVal, newVal, "Centering the pan from drag-and-drop did not change the transform")

  def test_11_panReset(self):
    global driver

    menuTree = driver.find_element_by_id("treeview_placeholder").find_element_by_tag_name('svg')
    menuTreeDisplay = menuTree.find_element_by_tag_name('g')
    ActionChains(driver).move_to_element(menuTree).drag_and_drop_by_offset(menuTree, 300, 200).perform()
    oldval = menuTreeDisplay.get_attribute("transform")
    button = driver.find_element_by_xpath("//button[contains(@onclick,'_resetAllNode')]")
    button.click()
    time.sleep(1)
    newVal = menuTreeDisplay.get_attribute("transform")
    self.assertNotEqual(oldval , newVal, "Resetting the pan from drag-and-drop did not change the transform")

if __name__ == '__main__':
  description = "Test the Install Timeline page of the ViViaN(TM) webpage"
  page = "vista_menus.php"
  webroot, driver, browser, is_local = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_menus)
  unittest.TextTestRunner(verbosity=2).run(suite)
