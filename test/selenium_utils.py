from selenium import webdriver
import os
import argparse
import time
def set_up(page):
    parser = argparse.ArgumentParser(description="Test the index page of the ViViAN tool, the VistA Package visualization")
    parser.add_argument("-r",dest = 'webroot', required=True, help="Web root of the ViViAN instance to test.  eg. http://code.osehra.org/vivian/")
    #parser.add_argument("-d",dest = 'outdir', required=False, help="Full path to directory to store coverage files")
    parser.add_argument("-cov",dest = 'cov',default=False, action='store_true', required=False, help="Attempt to save coverage at end of test?")
    result = vars(parser.parse_args())
    
    fp = webdriver.FirefoxProfile()

    fp.set_preference("browser.download.folderList",2)
    fp.set_preference("browser.download.manager.showWhenStarting",False)
    fp.set_preference("browser.download.dir",os.getcwd())
    fp.set_preference("browser.helperApps.neverAsk.saveToDisk", "text/plain")
    driver = webdriver.Firefox(fp)
    driver.maximize_window()
    driver.get(result['webroot'] + "/" + page)
    return driver,result

def shut_down(driver,inputArgs):
    if(inputArgs['cov']):
      driver.find_element_by_id("run_cov").click()
      time.sleep(1)
      driver.find_element_by_id("sav_cov").click()
      time.sleep(5)
    driver.close()