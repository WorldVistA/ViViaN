#---------------------------------------------------------------------------
# Copyright 2016 The Open Source Electronic Health Record Alliance
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

class test_installscale(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

  def test_01_packageAutocomplete(self):
    global driver
    packageAuto = driver.find_element_by_id('package_autocomplete')
    startDateBox = driver.find_element_by_id('timeline_date_start')
    origDate = startDateBox.get_attribute("value")
    packageAuto.clear()
    packageAuto.send_keys("Registration")
    time.sleep(2)
    driver.find_elements_by_class_name("ui-menu-item")[0].click()
    startDateBox = driver.find_element_by_id('timeline_date_start')
    endDate = startDateBox.get_attribute("value")
    self.assertTrue(endDate != origDate, "Start dates did not change")


  def test_02_installDateRangeSelect(self):
    global driver
    # Check the changing of the start date
    startDateBox = driver.find_element_by_id('timeline_date_start')
    axisLabels = driver.find_elements_by_class_name("tick")
    origDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    startDateBox.clear()
    startDateBox.send_keys("02/02/2000")
    driver.find_element_by_id('timeline_date_update').click()
    time.sleep(1)
    axisLabels = driver.find_elements_by_class_name("tick")
    endDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Changing of the timeline_date_start did not alter the timeline")

    #Check the changing of the end date
    stopDateBox = driver.find_element_by_id('timeline_date_stop')
    axisLabels = driver.find_elements_by_class_name("tick")
    origDate = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    stopDateBox.clear()
    stopDateBox.send_keys("02/02/2014")
    driver.find_element_by_id('timeline_date_update').click()
    time.sleep(1)
    axisLabels = driver.find_elements_by_class_name("tick")
    endDate = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Changing of the timeline_date_stop did not alter the timeline")


  def test_03_installDateRangeReset(self):
    global driver
    axisLabels = driver.find_elements_by_class_name("tick")
    origStartDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    origEndDate  = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    driver.find_element_by_id('timeline_date_reset').click()
    time.sleep(1)
    axisLabels = driver.find_elements_by_class_name("tick")
    newStartDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    newEndDate = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(newStartDate, origStartDate, "Changes were not set by to the original via Reset")
    self.assertNotEqual(origEndDate ,newEndDate, "Changes were not set by to the original via Reset")


  def test_04_installBarHover(self):
    global driver
    graphBars = driver.find_elements_by_class_name("bar")
    ActionChains(driver).move_to_element(graphBars[-1]).perform()
    tooltip = driver.find_element_by_id("toolTip")
    self.assertTrue(re.search(" [A-Z]+\*[0-9.]+\*[0-9]+",  tooltip.find_element_by_id("header1").text),"Header of tool tip didn't match expected format")
    self.assertTrue(re.search("[0-9]+\-[0-9]+\-[0-9]+", tooltip.find_element_by_id("installDate").text),"Install date of tool tip didnt match expected format")
    time.sleep(1)

  def test_05_installBarClick(self):
    global driver
    graphBars = driver.find_elements_by_class_name("bar")
    graphBars[-1].click()
    driver.switch_to.window(driver.window_handles[1])
    time.sleep(1)
    self.assertTrue(re.search("/files/9_7/9.7-[0-9]+.html", driver.current_url),"URL of new window didnt match expected format")
    driver.close()
    driver.switch_to.window(driver.window_handles[0])


if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="Test the Install Timeline page of the ViViaN(TM) webpage")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/installScale.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_installscale)
  unittest.TextTestRunner(verbosity=2).run(suite)