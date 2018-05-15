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
import argparse
import unittest
import re
import time

class test_pkgdep(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.quit()

  def test_01_chart_tooltip(self):
    global driver
    ActionChains(driver).move_to_element(driver.find_element_by_link_text("Uncategorized")).perform()
    tooltip = driver.find_element_by_id("toolTip")
    title = tooltip.find_element_by_id("header1").text

    self.assertTrue(re.search("Name:",title))
    self.assertTrue(re.search("Group:",title))

    depends = tooltip.find_element_by_id("dependency").text
    self.assertTrue(re.search("Depends:",depends))

    dependents = tooltip.find_element_by_id("dependents").text
    self.assertTrue(re.search("Dependents:",dependents))

  def test_02_chart_highlight(self):
    global driver

    # Values taken from local instance, not the most sustainable but works for now
    PCE_values = [40,23]
    ETiR_values = [0,6]
    CS_values = [3,0]

    # Three menu entries: to match each combination of depends/dependents
    ActionChains(driver).move_to_element(driver.find_element_by_link_text("PCE Patient Care Encounter")).perform()
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, PCE_values[0])
    self.assertEqual(target_size, PCE_values[1])
    time.sleep(3)

    ActionChains(driver).move_to_element(driver.find_element_by_link_text("Equipment Turn-In Request")).perform()
    time.sleep(3)
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, ETiR_values[0])
    self.assertEqual(target_size, ETiR_values[1])

    ActionChains(driver).move_to_element(driver.find_element_by_link_text("CORBA Services")).perform()
    time.sleep(3)
    source_size = len(driver.find_elements_by_class_name("node--source"))
    target_size = len(driver.find_elements_by_class_name("node--target"))
    self.assertEqual(source_size, CS_values[0])
    self.assertEqual(target_size, CS_values[1])

  def test_04_dep_bar_content(self):
    global driver

    driver.find_element_by_id('package-dependency').click()
    driver.find_element_by_id('bar-chart').click()
    time.sleep(1)

    chart_titles = ['VistA Packages Dependencies Chart','VistAPackageStatistics']
    dep_chart_legend = ['depends','dependents']
    dep_chart_top_entries = ['Kernel','Order Entry Results Reporting','Accounts Receivable']

    # Ensure that the data and labels of chart are not empty
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
    self.assertTrue(title_element.text in chart_titles)
    # Ensure that legends have proper information
    chart_legend = driver.find_element_by_class_name("highcharts-legend")
    legend_array = chart_legend.find_elements_by_tag_name("text")
    for item in legend_array:
       self.assertTrue(item.get_attribute("innerHTML") in dep_chart_legend)

    # Check each of the "Sort By" selections to ensure that the first element is found in top_entries array
    dep_pulldown = driver.find_element_by_id("list-dep")
    pulldown_options = Select(dep_pulldown)
    for option in pulldown_options.options:
      pulldown_options.select_by_visible_text(option.text)
      self.assertTrue(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text in dep_chart_top_entries)
      time.sleep(5)

  def test_05_bar_switch(self):
    global driver
    btn_group = driver.find_element_by_class_name("btn-group")
    for btn in btn_group.find_elements_by_tag_name("label"):
      type = btn.text.split(' ')[0]
      btn.click()
      self.assertTrue("active" in btn.get_attribute("class"))
      time.sleep(5)

  def test_06_stat_bar_content(self):
    global driver
    chart_titles = ['VistA Packages Dependencies Chart','VistA Package Statistics', 'VistAPackageStatistics']
    stat_chart_legend = ['routines','files','fields']
    stat_chart_top_entries = ['Generic Code Sheet','Integrated Billing','Accounts Receivable']

    #Ensure that the list of data and labels isn't empty
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
    self.assertTrue(title_element.text in chart_titles)

    # Ensure that legends have proper information
    chart_legend = driver.find_element_by_class_name("highcharts-legend")
    legend_array = chart_legend.find_elements_by_tag_name("text")
    for item in legend_array:
       self.assertTrue(item.get_attribute("innerHTML") in stat_chart_legend)

    # Check each of the "Sort By" selections to ensure that the first element is found in top_entries array
    dep_pulldown = driver.find_element_by_id("list-stats")
    pulldown_options = Select(dep_pulldown)
    for option in pulldown_options.options:
      pulldown_options.select_by_visible_text(option.text)
      self.assertTrue(driver.find_element_by_xpath("//*[@id='highcharts-0']/div/span[1]").text in stat_chart_top_entries)
      time.sleep(5)


if __name__ == '__main__':
  parser = argparse.ArgumentParser(description="")
  parser.add_argument("-r", dest='webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian")
  parser.add_argument("-b", dest='browser', default="FireFox", required=False, help="Web browser to use for testing [FireFox, Chrome]")
  result = vars(parser.parse_args())
  if result['browser'].upper() == "CHROME":
    driver = webdriver.Chrome()
  else:
    driver = webdriver.Firefox()
  driver.maximize_window()
  driver.get(result['webroot'] + "/vista_pkg_dep.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_pkgdep)
  unittest.TextTestRunner(verbosity=2).run(suite)
