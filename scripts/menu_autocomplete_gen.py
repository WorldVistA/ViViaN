import json
import glob

def run(fileNo,outName):
  output = "../files/%s" % outName
  menuJsonFiles = glob.glob("../files/menus/%s/VistAMenu-*.json" % fileNo )
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
  run("19","menu_autocomplete.json")
  run("101","protocol_menu_autocomplete.json")
