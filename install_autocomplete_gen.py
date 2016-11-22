import json
import glob

def run():
  output = "install_autocomplete.json"
  installInformation = json.load(open("files/install_information.json", 'r'))

  with open("install_autocomplete.json", 'w') as installAutocompleteOutputFile:
    packageNames = installInformation.keys();
    packageNames.sort()
    json.dump(packageNames, installAutocompleteOutputFile)

if __name__ == '__main__':
  run()
