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

from builtins import str
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
    driver.quit()

  # TODO: This test was written for the circular graph, create an equivalent
  #       test for the bar chart
  def DISABLE_test_01_dep_bar_tooltip(self):
    pass

  def test_02_dep_bar_content(self):
    # Ensure that the there is some data for both depends and dependents
    chart_container = driver.find_elements_by_class_name("highcharts-series")
    for type in chart_container:  # depends, dependents
      rects = type.find_elements_by_tag_name("rect")
      for rect in rects:
          if int(rect.get_attribute("height")) > 0:
              break
      else:
          self.fail("Failed to find data in Dependency Chart")

    # Check for data labels - make sure they exist and aren't all "0"
    labels_list = driver.find_elements_by_class_name("highcharts-data-labels")
    for type in labels_list:  # depends, dependents
      labels = type.find_elements_by_tag_name("text")
      for label in labels:
          if label.text != "0":
              break
      else:
          self.fail("Failed to find data labels in Dependency Chart")

    # Ensure that each package is only included once
    labels = driver.find_elements_by_class_name("highcharts-xaxis-labels")[1]
    package_names = []
    for item in labels.find_elements_by_xpath(".//a[@href]"):
        package_names.append(str(item.get_attribute('innerHTML')))
    self.assertEqual(len(package_names), len(set(package_names)), "Package names should be unique")

    # Read title of graph
    title_element = driver.find_element_by_class_name("highcharts-title")
    self.assertEqual(title_element.text, 'VistA Packages Dependencies Chart')

    # Ensure that legends have proper information
    dep_chart_legend = ['depends', 'dependents']
    chart_legend = driver.find_element_by_class_name("highcharts-legend")
    legend_array = chart_legend.find_elements_by_tag_name("text")
    for item in legend_array:
       self.assertTrue(item.get_attribute("innerHTML") in dep_chart_legend)

  def test_03_dep_bar_sort(self):
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

  def test_04_bar_switch(self):
    btn_group = driver.find_element_by_class_name("btn-group")
    for btn in btn_group.find_elements_by_tag_name("label"):
      type = btn.text.split(' ')[0]
      btn.click()
      self.assertTrue("active" in btn.get_attribute("class"))
      time.sleep(5)

    def DISABLE_test_05_stats_bar_tooltip(self):
      pass

    def test_06_stats_bar_content(self):
      # Ensure that the there is some data for both routines, files and fields
      chart_container = driver.find_elements_by_class_name("highcharts-series")
      for type in chart_container:  # routines, files and fields
        rects = type.find_elements_by_tag_name("rect")
        for rect in rects:
            if rect.get_attribute("height") > 0:
                break
        else:
            self.fail("Failed to find data in Stats Chart")

      # Check for data labels - make sure they exist and aren't all "0"
      labels_list = driver.find_elements_by_class_name("highcharts-data-labels")
      for type in labels_list:  # routines, files and fields
        labels = type.find_elements_by_tag_name("text")
        for label in labels:
            if label.text != "0":
                break
        else:
            self.fail("Failed to find data labels in Stats Chart")

      # Ensure that each package is only included once
      labels = driver.find_elements_by_class_name("highcharts-xaxis-labels")[1]
      package_names = []
      for item in labels.find_elements_by_xpath(".//a[@href]"):
          package_names.append(str(item.get_attribute('innerHTML')))
      self.assertEqual(len(package_names), len(set(package_names)), "Package names should be unique")

      # Read title of graph
      title_element = driver.find_element_by_class_name("highcharts-title")
      self.assertEqual(title_element.text, 'VistA Package Statistics')

      # Ensure that legends have proper information
      stat_chart_legend = ['routines', 'files', 'fields']
      chart_legend = driver.find_element_by_class_name("highcharts-legend")
      legend_array = chart_legend.find_elements_by_tag_name("text")
      for item in legend_array:
         self.assertTrue(item.get_attribute("innerHTML") in stat_chart_legend)

    def test_03_dep_bar_sort(self):
      # Check each of the "Sort By" selections to ensure that
      # the first element is found in top_entries array
      dep_pulldown = driver.find_element_by_id("list-dep")
      pulldown_options = Select(dep_pulldown)

      # Name
      pulldown_options.select_by_visible_text("Name")
      time.sleep(5)
      self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "A1BF Response Time")

      # Routines
      pulldown_options.select_by_visible_text("Routines")
      time.sleep(5)
      self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "Automated Information Collection System")

      # Files
      pulldown_options.select_by_visible_text("Files")
      time.sleep(5)
      self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "Integrated Billing")

      # Fields
      pulldown_options.select_by_visible_text("File Fields")
      time.sleep(5)
      self.assertEqual(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text, "Integrated Billing")


if __name__ == '__main__':
  description = "Test package dependency bar chart"
  page = "vivian/vista_pkg_dep_chart.php"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_pkgdepchart)
  unittest.TextTestRunner(verbosity=2).run(suite)
