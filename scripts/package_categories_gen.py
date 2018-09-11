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
