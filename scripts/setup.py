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

def run():
  # input: ../files/menus/VistAMenu-*.json
  # output: ../files/menu_autocomplete.json
  menu_autocomplete_gen.run()
  print "*** Updated ../files/menu_autocomplete.json"

  # input: ../files/menus/VistAMenu-*.json
  # output: ../files/option_autocomplete.json
  option_autocomplete_gen.run()
  print "*** Updated ../files/option_autocomplete.json"

  # input: ../files/[...]/*-RPC.html and files/[...]/*-HL7.html
  # output: ../files/PackageInterface.csv
  pkg_interface_gen.run()
  print "*** Updated ../files/PackageInterface.csv"

  # input: PackageDes.json, ../PackageCategories.json, ../Packages.csv
  #        and ../files/PackageInterface.csv
  # output: ../files/packages.json and ../files/packages_autocomplete.json
  pkgcsv_to_json.generate_packages_json()
  print "*** Updated ../files/packages.json"
  print "*** Updated ../files/packages_autocomplete.json"

  # input: ../files/install_information.json
  # output: ../files/install_autocomplete.json
  install_autocomplete_gen.run()
  print "*** Updated ../files/install_autocomplete.json"

  # input: VHA_BFF_version2-10.xlsx
  # output: ../files/bff.json
  BFFExcel2Json.convertBFFExcelToJson("BFF_version_2-12.xlsx", "../files/bff.json")
  print "*** Updated ../files/bff.json"

if __name__ == '__main__':
  run()

