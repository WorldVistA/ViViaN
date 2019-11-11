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
from selenium.webdriver.support.ui import Select
from vivian_test_utils import setup_webdriver
import unittest
import re
import time

class test_icrtable(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def test_01_max_rows(self):
    time.sleep(3)
    table = driver.find_element_by_id("ICR_List-All")
    odd_rows = len(table.find_elements_by_class_name('odd'))
    even_rows = len(table.find_elements_by_class_name('even'))
    total_rows = odd_rows + even_rows

    max_rows = 10
    self.assertEqual(total_rows, max_rows)


  def test_02_filter(self):
    # Display up to 50 rows
    length_select = Select(driver.find_element_by_name("ICR_List-All_length"))
    length_select.select_by_value('50')
    time.sleep(1)

    table = driver.find_element_by_id("ICR_List-All")
    odd_rows = len(table.find_elements_by_class_name('odd'))
    even_rows = len(table.find_elements_by_class_name('even'))
    total_rows = odd_rows + even_rows
    self.assertEqual(50, total_rows)

    # Do a global search
    search_text = "dbia42"
    search_element = driver.find_elements_by_xpath("//*[@type='search']")[0]
    search_element.clear()
    search_element.send_keys(search_text)
    time.sleep(1)

    odd_rows1 = len(table.find_elements_by_class_name('odd'))
    even_rows1 = len(table.find_elements_by_class_name('even'))
    total_rows1 = odd_rows1 + even_rows1
    self.assertTrue(total_rows > total_rows1)

    footer = table.find_elements_by_tag_name("tfoot")[0]
    filter_elements = footer.find_elements_by_tag_name("th")

    ia_search_text = "3"
    ia_search_element = table.find_element_by_name("IA #")
    ia_search_element.clear()
    ia_search_element.send_keys(ia_search_text)
    time.sleep(1)

    odd_rows2 = len(table.find_elements_by_class_name('odd'))
    even_rows2 = len(table.find_elements_by_class_name('even'))
    total_rows2 = odd_rows2 + even_rows2
    self.assertTrue(total_rows2 < total_rows1)

    usage_select = Select(table.find_element_by_name("Usage"))
    usage_select.select_by_value('Private')

    odd_rows3 = len(table.find_elements_by_class_name('odd'))
    even_rows3 = len(table.find_elements_by_class_name('even'))
    total_rows3 = odd_rows3 + even_rows3
    self.assertTrue(total_rows3 < total_rows2)


  def _test_03_clear(self):
    table = driver.find_element_by_id("ICR_List-All")
    odd_rows = len(table.find_elements_by_class_name('odd'))
    even_rows = len(table.find_elements_by_class_name('even'))
    total_rows = odd_rows + even_rows

    # Do a global search for a string that won't be found
    search_text = "lsjdjlksdjf"
    search_element = driver.find_elements_by_xpath("//*[@type='search']")[0]
    search_element.clear()
    search_element.send_keys(search_text)
    time.sleep(1)

    empty_row = table.find_elements_by_class_name('odd')[0].find_elements_by_class_name('dataTables_empty')[0]
    self.assertEqual("No matching records found", empty_row.text)

    # clear search
    driver.find_element_by_tag_name("button").click()

    odd_rows2 = len(table.find_elements_by_class_name('odd'))
    even_rows2 = len(table.find_elements_by_class_name('even'))
    total_rows2 = odd_rows2 + even_rows2
    self.assertEqual(total_rows, total_rows2)


if __name__ == '__main__':
  description = "Test the ICR table pages of the ViViaN(TM) tool, the VistA Package visualization"
  page = "files/ICR/All-ICR List.html"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_icrtable)
  unittest.TextTestRunner(verbosity=2).run(suite)
