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
from selenium.webdriver.support.ui import Select
from vivian_test_utils import setup_webdriver
import unittest
import re
import time

class test_pkgdep(unittest.TestCase):
  @classmethod
  def tearDownClass(cls):
    global driver
    driver.quit()

  def test_01_tooltip(self):
    global driver
    time.sleep(10)

    # Choose a package that's visible when the screen is first loaded
    package = "Lab Service"
    group = "Provide General Laboratory Services"

    element = driver.find_element_by_xpath("//*[contains(text(),'" + package +"')]");
    ActionChains(driver).move_to_element(element).perform()
    tooltip = driver.find_element_by_id("toolTip")

    title = tooltip.find_element_by_id("header1").text
    self.assertTrue(re.search("Name: " + package, title))
    self.assertTrue(re.search("Group: " + group, title))

    depends = tooltip.find_element_by_id("dependency").text
    self.assertTrue(re.search("Depends:", depends))

    dependents = tooltip.find_element_by_id("dependents").text
    self.assertTrue(re.search("Dependents:", dependents))

    dependents = tooltip.find_element_by_id("bothDeps").text
    self.assertTrue(re.search("Both:", dependents))

  def test_02_highlight(self):
    global driver

    global browser
    if browser == "FIREFOX":
      return # Test fails on FireFox, skip it for now

    # Values taken from most-recent code.osehra.org instance,
    # not the most sustainable but works for now
    PCE_values = [43,29]
    ETiR_values = [0,6]
    CS_values = [3,0]

    # Three menu entries: to match each combination of depends/dependents
    package = "PCE Patient Care Encounter"
    element = driver.find_element_by_xpath("//*[contains(text(),'" + package +"')]")
    ActionChains(driver).move_to_element(element).perform()
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, PCE_values[0])
    self.assertEqual(target_size, PCE_values[1])
    time.sleep(3)

    package = "Equipment Turn-In Request"
    element = driver.find_element_by_xpath("//*[contains(text(),'" + package +"')]")
    ActionChains(driver).move_to_element(element).perform()
    time.sleep(3)
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, ETiR_values[0])
    self.assertEqual(target_size, ETiR_values[1])

    package = "CORBA Services"
    element = driver.find_element_by_xpath("//*[contains(text(),'" + package +"')]")
    ActionChains(driver).move_to_element(element).perform()
    time.sleep(3)
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, CS_values[0])
    self.assertEqual(target_size, CS_values[1])

if __name__ == '__main__':
  description = "Test package dependency circulr layout"
  page = "vista_pkg_dep.php"
  webroot, driver, browser, is_local = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_pkgdep)
  unittest.TextTestRunner(verbosity=2).run(suite)
