""" Python script to parse the packages.json file
    and generate the PackageCategories.json.
"""

import json
import logging

def traverseChildren(node):
  if "children" in node:
    for child in node['children']:
      traverseChildren(child)
  else:
    """ only keep the name """
    for key in node.keys():
      if key != 'name':
        del node[key]

def main():
  pkgjson = json.load(open("../files/packages.json", 'rb'))
  traverseChildren(pkgjson)
  json.dump(pkgjson, open("../files/PackageCategories.json", 'w'), indent=4)

if __name__ == '__main__':
  main();
