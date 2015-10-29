""" Python script to generate or update ViViAN data

    This script is a short-cut to calling each set-up
    script individually.
"""

import menu_autocomplete_gen
import option_autocomplete_gen
import pkg_interface_gen
import pkgcsv_to_json

def run():
  # input: menus/VistAMenu-*.json
  # output: menu_autocomplete.json
  menu_autocomplete_gen.run()
  print "*** Updated menu_autocomplete.json"

  # input: menus/VistAMenu-*.json
  # output: option_autocomplete.json
  option_autocomplete_gen.run()
  print "*** Updated option_autocomplete.json"

  # input: files/*-RPC.html and files/*-HL7.html
  # output: PackageInterface.csv
  pkg_interface_gen.run()
  print "*** Updated PackageInterface.csv"

  # input: PackageCategories.json, Packages.csv and PackageInterface.csv
  # output: packages.json, packages_autocomplete.json
  pkgcsv_to_json.generate_packages_json()
  print "*** Updated packages.json"
  print "*** Updated packages_autocomplete.json"


if __name__ == '__main__':
  run()

