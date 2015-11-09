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

class test_about(unittest.TestCase):

  @classmethod
  def tearDownClass(cls):
    global driver
    driver.close()

  def test_about_option(self):
    global driver
    about_option = driver.find_element_by_xpath('//*[@id="demoexamples"]/ul/li[5]/a')
    about_option.click()
    time.sleep(1)
    about_header = driver.find_element_by_class_name('ui-state-focus')

    about_para = driver.find_element_by_id("dialog-modal-about")
    self.assertTrue(re.search("Visualizing VistA And Namespace",about_para.text))
    self.assertTrue(re.search("tree-based visualizations",about_para.text))
    self.assertTrue(re.search("VHA Business Function Framework",about_para.text))


if __name__ == '__main__':
  parser =argparse.ArgumentParser(description="Access the 'About' Text of the ViViaN(TM) webpage")
  parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViaN(TM) instance to test.  eg. http://code.osehra.org/vivian/")
  result = vars(parser.parse_args())
  driver = webdriver.Firefox()
  driver.get(result['webroot'] + "/index.php")
  suite = unittest.TestLoader().loadTestsFromTestCase(test_about)
  unittest.TextTestRunner(verbosity=2).run(suite)