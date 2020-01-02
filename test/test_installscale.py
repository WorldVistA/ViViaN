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
from vivian_test_utils import setup_webdriver
import unittest
import re
import time

class test_installscale(unittest.TestCase):
  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def test_01_packageAutocomplete(self):
    time.sleep(10)
    packageAuto = driver.find_element_by_id('package_autocomplete')
    startDateBox =  driver.find_element_by_id('timeCtl').find_elements_by_class_name("tick")[0].find_element_by_tag_name('text')
    origDate = startDateBox.text
    packageAuto.clear()
    packageAuto.send_keys("Clinical Procedures")
    time.sleep(2)
    driver.find_elements_by_class_name("ui-menu-item")[0].click()
    startDateBox = driver.find_element_by_id('timeCtl').find_elements_by_class_name("tick")[0].find_element_by_tag_name('text')
    endDate = startDateBox.text
    self.assertTrue(endDate != origDate, "Start dates did not change")


  def test_02_installDateRangeSelect(self):
    # Check the changing of the start date
    ctrlAxisLabels = driver.find_element_by_id('timeCtl').find_elements_by_class_name("tick")
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    origDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    ActionChains(driver).move_to_element(ctrlAxisLabels[2]).drag_and_drop(ctrlAxisLabels[2].find_element_by_tag_name('line'), ctrlAxisLabels[4].find_element_by_tag_name('line')).perform()
    time.sleep(3)
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    endDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Changing of the timeline_date via brush control did not alter the timeline")

  def test_03_installDateRangeSelectManip(self):
    # Use cursor to move bar
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    origDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    activeBox = driver.find_element_by_class_name("extent")
    ActionChains(driver).move_to_element(activeBox).drag_and_drop_by_offset(activeBox, 200, 0).perform()
    time.sleep(3)
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    endDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Moving 'Extent' box of active area by clicking and dragging did not alter the timeline")

    # Use cursor to shrink bar
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    origDate = displayAxisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    activeBoxLeft = driver.find_elements_by_class_name("resize")[0]
    ActionChains(driver).move_to_element(activeBoxLeft).drag_and_drop_by_offset(activeBoxLeft, 50, 0).perform()
    time.sleep(5)
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    endDate = displayAxisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Shrinking of the 'Extent' box from the right did not alter the timeline")

    # Use cursor to shrink bar
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    endDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    activeBoxRight = driver.find_elements_by_class_name("resize")[1]
    ActionChains(driver).move_to_element(activeBoxRight).drag_and_drop_by_offset(activeBoxRight, -50, 0).perform()
    time.sleep(3)
    displayAxisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    endDate = displayAxisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(endDate, origDate, "Shrinking of the 'Extent' box from the left did not alter the timeline")

  def test_04_installDateRangeReset(self):
    axisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")

    origStartDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    origEndDate  = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    driver.find_element_by_id('timeline_date_reset').click()
    time.sleep(1)
    axisLabels = driver.find_element_by_id('timeline').find_elements_by_class_name("tick")
    newStartDate = axisLabels[0].find_element_by_tag_name('text').get_attribute("innerHTML")
    newEndDate = axisLabels[-1].find_element_by_tag_name('text').get_attribute("innerHTML")
    self.assertNotEqual(newStartDate, origStartDate, "Changes were not set by to the original via Reset")
    self.assertNotEqual(origEndDate, newEndDate, "Changes were not set by to the original via Reset")

  def test_05_installBarHover(self):
    graphBars = driver.find_elements_by_class_name("bar")
    # Scroll bar into view
    graphBars[0].location_once_scrolled_into_view
    ActionChains(driver).move_to_element(graphBars[0]).perform()
    tooltip = driver.find_element_by_id("toolTip")
    self.assertTrue(re.search(" [A-Z]+\*[0-9.]+\*[0-9]+",  tooltip.find_element_by_id("header1").text), "Header of tool tip didn't match expected format")
    self.assertTrue(re.search("[0-9]+\-[0-9]+\-[0-9]+", tooltip.find_element_by_id("installDate").text), "Install date of tool tip didnt match expected format")
    time.sleep(1)

  def test_06_installBarClick(self):
    graphBars = driver.find_elements_by_class_name("bar")
    graphBars[-1].click()
    driver.switch_to.window(driver.window_handles[1])
    time.sleep(1)
    self.assertTrue(re.search("/vivian-data/9_7/9.7-[0-9]+.html", driver.current_url), "URL of new window didnt match expected format")
    driver.close()
    driver.switch_to.window(driver.window_handles[0])


if __name__ == '__main__':
  description = "Test the Install Timeline page of the ViViaN(TM) webpage"
  page = "vivian/installScale.php"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_installscale)
  unittest.TextTestRunner(verbosity=2).run(suite)
