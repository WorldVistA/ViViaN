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
import os

import shutil

def run():
  files_dir = "../../files"

  # input: <files_dir>/menus/19/VistAMenu-*.json
  # output: <files_dir>/menu_autocomplete.json
  menu_autocomplete_gen.run("19", "menu_autocomplete.json", files_dir)

  # input: <files_dir>/menus/101/VistAMenu-*.json
  # output: <files_dir>/protocol_menu_autocomplete.json
  menu_autocomplete_gen.run("101", "protocol_menu_autocomplete.json", files_dir)

  # input: <files_dir>/menus/19/VistAMenu-*.json
  # output: <files_dir>/option_autocomplete.json
  option_autocomplete_gen.run("19","option_autocomplete.json", files_dir)

  # input: <files_dir>/menus/101/VistAMenu-*.json
  # output: <files_dir>/option_autocomplete.json
  option_autocomplete_gen.run("101","protocol_option_autocomplete.json", files_dir)

  # input: <files_dir>/[...]/*-RPC.html and <files_dir>/[...]/*-HL7.html
  # output: <files_dir>/PackageInterface.csv
  pkg_interface_gen.run(files_dir)

  # input: PackageDes.json, ../PackageCategories.json, ../Packages.csv
  #        and <files_dir>/PackageInterface.csv
  # output: <files_dir>/packages.json and <files_dir>/packages_autocomplete.json
  pkgcsv_to_json.run(files_dir)

  # input: <files_dir>/install_information.json
  # output: <files_dir>/install_autocomplete.json
  install_autocomplete_gen.run(files_dir)

  # input: VHA_BFF_version2-10.xlsx
  # output: <files_dir>/bff.json
  BFFExcel2Json.run("BFF_version_2-12.xlsx", "%s/bff.json" % files_dir)

  # Move ../himData.json to <files_dir>/himData.json
  shutil.copyfile("../himData.json", "%s/himData.json" % files_dir)
  print("*** Copied %s/himData.json" % files_dir)

if __name__ == '__main__':
  run()

