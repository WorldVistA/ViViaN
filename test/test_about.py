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
from vivian_test_utils import setup_webdriver
import unittest
import re
import time

class test_about(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    driver.quit()

  def test_01_about_option(self):
    about_option = driver.find_element_by_xpath('//*[@id="navigation_buttons"]/nav/div/ul[2]/li[1]/a')
    about_option.click()
    time.sleep(1)

    about_para = driver.find_element_by_id("dialog-modal-about")
    self.assertTrue(re.search("Visualizing VistA and Namespace", about_para.text))
    self.assertTrue(re.search("tree-based visualizations", about_para.text))
    self.assertTrue(re.search("VHA Business Function Framework", about_para.text))

if __name__ == '__main__':
  description = "Access the 'About' Text of the ViViaN(TM) webpage"
  page = "vivian/index.php"
  webroot, driver, browser = setup_webdriver(description, page)
  suite = unittest.TestLoader().loadTestsFromTestCase(test_about)
  unittest.TextTestRunner(verbosity=2).run(suite)
