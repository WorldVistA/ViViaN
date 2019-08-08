#---------------------------------------------------------------------------
# Copyright 2018 The Open Source Electronic Health Record Alliance
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

""" Python script to generate or update ViViAN data

    This script is a short-cut to calling each set-up
    script individually.
"""

import install_autocomplete_gen
import menu_autocomplete_gen
import option_autocomplete_gen
import pkg_interface_gen
import pkgcsv_to_json
import BFFExcel2Json

import shutil

def run():
  # input: ../files/menus/19/VistAMenu-*.json
  # output: ../files/menu_autocomplete.json
  menu_autocomplete_gen.run("19","menu_autocomplete.json")
  print("*** Updated ../files/menu_autocomplete.json")

  # input: ../files/menus/101/VistAMenu-*.json
  # output: ../files/protocol_menu_autocomplete.json
  menu_autocomplete_gen.run("101","protocol_menu_autocomplete.json")
  print("*** Updated ../files/protocol_menu_autocomplete.json")

  # input: ../files/menus/19/VistAMenu-*.json
  # output: ../files/option_autocomplete.json
  option_autocomplete_gen.run("19","option_autocomplete.json")
  print("*** Updated ../files/option_autocomplete.json")

  # input: ../files/menus/101/VistAMenu-*.json
  # output: ../files/option_autocomplete.json
  option_autocomplete_gen.run("101","protocol_option_autocomplete.json")
  print("*** Updated ../files/protocol_option_autocomplete.json")

  # input: ../files/[...]/*-RPC.html and files/[...]/*-HL7.html
  # output: ../files/PackageInterface.csv
  pkg_interface_gen.run()
  print("*** Updated ../files/PackageInterface.csv")

  # input: PackageDes.json, ../PackageCategories.json, ../Packages.csv
  #        and ../files/PackageInterface.csv
  # output: ../files/packages.json and ../files/packages_autocomplete.json
  pkgcsv_to_json.generate_packages_json()
  print("*** Updated ../files/packages.json")
  print("*** Updated ../files/packages_autocomplete.json")

  # input: ../files/install_information.json
  # output: ../files/install_autocomplete.json
  install_autocomplete_gen.run()
  print("*** Updated ../files/install_autocomplete.json")

  # input: VHA_BFF_version2-10.xlsx
  # output: ../files/bff.json
  BFFExcel2Json.convertBFFExcelToJson("BFF_version_2-12.xlsx", "../files/bff.json")
  print("*** Updated ../files/bff.json")

  # Move ../himData.json to ../files/himData.json
  shutil.copyfile("../himData.json", "../files/himData.json")

if __name__ == '__main__':
  run()

