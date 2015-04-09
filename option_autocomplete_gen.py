import json
import glob



output = "option_autocomplete.json"
menuJsonFiles = glob.glob("menus/VistAMenu-*.json")
outjson = []
parent_id = '';

def recurse_info(inJSON):
    menuItem = {}
    if 'option' in inJSON:
      menuItem['label'] = inJSON['option'] + ': ' + inJSON['name']
    else:
      menuItem['label'] = 'UNK: ' + inJSON['name']
    menuItem['id'] = inJSON['ien']
    menuItem['parent_id'] = parent_id;
    print "*****" + str(menuItem)
    outjson.append(menuItem)
    if 'children' in inJSON:
      for child in inJSON['children']:
        recurse_info(child)

for menuFile in menuJsonFiles:
  menuItem = {}
  with open(menuFile, 'r') as menuFp:
    menuJson = json.load(menuFp);
    parent_id = menuJson['ien']
    recurse_info(menuJson)
with open(output, 'w') as outFp:
  json.dump(outjson, outFp)