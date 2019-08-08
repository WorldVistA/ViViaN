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
import json
import re

def run():
  # Navigate to non-frame version
  driver = webdriver.Firefox()
  #driver = webdriver.Chrome()
  driver.get("https://him.osehra.org/content/_Z.x.p.iA.l.y.vE.eGK.k79K-A.nV2A-top-summary.html")

  # Get a list of all of the packages
  package_names = []
  elements = driver.find_elements_by_xpath('//area')
  for element in elements:
    package_names.append(element.get_attribute('alt'))
  package_names.sort()

  generated_packages = {}
  for name in package_names:
    try:
      element = driver.find_element_by_xpath("//area[@alt='" + name +"']")
      generated_packages[_convert(name)] = element.get_attribute("href")
      element.click()
      if name == 'Pharmacy':
        subpackages = driver.find_elements_by_xpath('//area')
        for subpackage in subpackages:
          subpackage_name = subpackage.get_attribute("alt")
          if not "Main" == subpackage_name and not "Enumerations" in subpackage_name:
            generated_packages[_convert(subpackage_name)] = subpackage.get_attribute("href")
    except Exception as e:
      print "Failed to parse package " + name
      print e
    finally:
      driver.back()

  # write new file
  #print "\nWriting data to himData_new.json\n"
  #with open('himData_new.json', 'w') as outfile:
  #  json.dump(generated_packages, outfile)

  # Print summary of changes
  with open('../himData.json') as data_file:
    data = json.load(data_file)

  new_packages = {}
  for name, href in generated_packages.items():
    if not name in data.keys():
      new_packages[name] = href
    elif 'https://him.osehra.org/content/' + data[name] != href:
      print "Link is different for", name
      print "    Found    ", href
      print "    Expected ", 'https://him.osehra.org/content/',data[name]

  print "\nFound the following new packages:\n"
  to_skip = ["Barcoding", # Note: Using the link from Pharmacy/BCMA
             "Common"]
  new_package_names = new_packages.keys()
  new_package_names.sort()
  for new_package in new_package_names:
    if new_package not in to_skip:
      print new_package, new_packages[new_package]

  removed_packages = []
  for name, href in data.items():
    if not name in generated_packages.keys():
      removed_packages.append(name)

  removed_packages.sort()
  print "\nMissing the following packages:\n"
  print "\n".join(removed_packages)

  driver.close()

def _convert(name):
  special_cases = {"AllergiesAdverseReactions": "Adverse Reaction Tracking",
                   "AutomatedMedInfoExch": "Automated Medical Information Exchange",
                   "BCMA": "Barcode Medication Administration",
                   "Consults": "Consult Request Tracking",
                   "CPT-HCPCSCodes": "CPT HCPCS Codes",
                   "EClaimsMgmtEngine" : "E Claims Management Engine",
                   "EmergencyDepartment": "Emergency Department Integration Software",
                   "Immunizations": "Immunization", # TODO: Is this the correct mapping?
                   "MailMan": "MailMan",
                   "MRSAInitiativeReports": "Methicillin Resistant Staph Aurerus Initiative Reports",
                   "Orders": "Order Entry Results Reporting",
                   "PatientCareEncounter": "PCE Patient Care Encounter",
                   "RegistrationEnrollmentEligibility" : "Registration",
                   "TextIntegrationUtilities" : "Text Integration Utility",
                   "VistaLink": "VistALink",
                   "Vitals": "General Medical Record - Vitals"}
  # Remove leading and trailing characters
  name = name.strip()
  if name in special_cases.keys():
    return special_cases[name]
  elif name.find(' ') >= 0:
    return name
  else:
    s1 = re.sub('(.)([A-Z][a-z]+)', r'\1 \2', name)
    s = re.sub('([a-z0-9])([A-Z])', r'\1 \2', s1)
    s.replace("Vist A", "VistA")
    return s

if __name__ == '__main__':
  run()
