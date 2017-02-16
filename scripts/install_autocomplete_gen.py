import json
import glob

def run():
  output = "../files/install_autocomplete.json"
  input = "../files/install_information.json"

  installInformation = json.load(open(input, 'r'))

  with open(output, 'w') as installAutocompleteOutputFile:
    packageNames = installInformation.keys();
    packageNames.sort()
    json.dump(packageNames, installAutocompleteOutputFile)

if __name__ == '__main__':
  run()
