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
    if 'children' in inJSON:
      for child in inJSON['children']:
        recurse_info(child)

def run():
  global parent_id
  global parent_name
  output = "option_autocomplete.json"
  menuJsonFiles = glob.glob("files/menus/VistAMenu-*.json")

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
  run()
