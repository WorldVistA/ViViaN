import json
import glob

def run():
  output = "menu_autocomplete.json"
  menuJsonFiles = glob.glob("files/menus/VistAMenu-*.json")
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

if __name__ == '__main__':
  run()
