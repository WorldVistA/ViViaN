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

import json
import glob

outjson = []
parent_id = ''
parent_name= ''

def recurse_info(inJSON):
    menuItem = {}
    if 'option' in inJSON:
      menuItem['label'] = inJSON['option'] + ': ' + inJSON['name']
    else:
      menuItem['label'] = 'UNK: ' + inJSON['name']
    menuItem['id'] = inJSON['ien']
    menuItem['parent_id'] = parent_id
    menuItem['parent_name'] = parent_name
    if(not (menuItem in outjson)):
      outjson.append(menuItem)
    if '_children' in inJSON:
      for child in inJSON['_children']:
        recurse_info(child)

def run(fileNo,outName):
  global outjson
  outjson=[]
  global parent_id
  global parent_name
  output = "../files/%s" % outName
  menuJsonFiles = glob.glob("../files/menus/%s/VistAMenu-*.json" % fileNo )

  for menuFile in menuJsonFiles:
    menuItem = {}
    with open(menuFile, 'r') as menuFp:
      menuJson = json.load(menuFp)
      parent_id = menuJson['ien']
      parent_name = menuJson['name']
      recurse_info(menuJson)

  with open(output, 'w') as outFp:
    json.dump(outjson, outFp)


if __name__ == '__main__':
  run("19","option_autocomplete.json")
  run("101","protocol_option_autocomplete.json")
