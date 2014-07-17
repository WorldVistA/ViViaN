import json
import glob

output = "menu_autocomplete.json"
menuJsonFiles = glob.glob("menus/VistAMenu-*.json")
outjson = []
for menuFile in menuJsonFiles:
  menuItem = {}
  with open(menuFile, 'r') as menuFp:
    menuJson = json.load(menuFp);
    menuItem['label'] = menuJson['option'] + ': ' + menuJson['name']
    menuItem['id'] = menuJson['ien']
    outjson.append(menuItem)
with open(output, 'w') as outFp:
  json.dump(outjson, outFp)

  
