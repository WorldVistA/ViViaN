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

class test_pkgdepchart(unittest.TestCase):
  @classmethod
  def tearDownClass(cls):
    global driver
    driver.quit()

  # TODO: This test was written for the circular graph, create an equivalent
  #       test for the bar chart
  def DISABLE_test_01_chart_tooltip(self):
    global driver
    time.sleep(15)

    ActionChains(driver).move_to_element(driver.find_element_by_link_text("Scheduling")).perform()
    tooltip = driver.find_element_by_id("tooltip")
    title = tooltip.find_element_by_id("header1").text

    self.assertTrue(re.search("Name:", title))
    self.assertTrue(re.search("Group:", "Patient Service Delivery"))

    depends = tooltip.find_element_by_id("dependency").text
    self.assertTrue(re.search("Depends:", depends))

    dependents = tooltip.find_element_by_id("dependents").text
    self.assertTrue(re.search("Dependents:", dependents))

    dependents = tooltip.find_element_by_id("both").text
    self.assertTrue(re.search("Both:", dependents))

  def test_02_dep_bar_content(self):
    global driver

    # TODO: This test will pass even if there's no data!!
    #       There will still be empty rectangles!
    # Ensure that the list of data and labels isn't empty
    chart_container = driver.find_elements_by_class_name("highcharts-series")
    for type in chart_container:
      bar_array = type.find_elements_by_tag_name("rect")
      self.assertTrue(len(bar_array) > 0)

    chart_container = driver.find_elements_by_class_name("highcharts-data-labels")
    for type in chart_container:
      bar_array = type.find_elements_by_tag_name("text")
      self.assertTrue(len(bar_array) > 0)

    # Read title of graph
    title_element = driver.find_element_by_class_name("highcharts-title")
    self.assertEqual(title_element.text, 'VistA Packages Dependencies Chart')

    # Ensure that legends have proper information
    dep_chart_legend = ['depends','dependents']
    chart_legend = driver.find_element_by_class_name("highcharts-legend")
    legend_array = chart_legend.find_elements_by_tag_name("text")
    for item in legend_array:
       self.assertTrue(item.get_attribute("innerHTML") in dep_chart_legend)

  def test_03_dep_bar_sort(self):
    global driver
    # Check each of the "Sort By" selections to ensure that
    # the first element is found in top_entries array
    dep_pulldown = driver.find_element_by_id("list-dep")
    pulldown_options = Select(dep_pulldown)
    # Name
    pulldown_options.select_by_visible_text("Name")
    time.sleep(5)
    self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "A1BF Response Time")

    # Dependencies
    pulldown_options.select_by_visible_text("Dependencies")
    time.sleep(5)
    self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "Order Entry Results Reporting") #in dep_chart_top_entries)

    # Dependents
    pulldown_options.select_by_visible_text("Dependents")
    time.sleep(5)
    self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "Kernel")


if __name__ == '__main__':
  description = "Test package dependency bar chart"
  page = "vista_pkg_dep_chart.php"
  webroot, driver, browser, is_local = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_pkgdepchart)
  unittest.TextTestRunner(verbosity=2).run(suite)
