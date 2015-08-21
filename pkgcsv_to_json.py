import csv
import json

DISTR_LIST = ("VA","OSEHRA","DSS","Medsphere","Oroville")

def generate_packages_json():
  pkgCatJson = json.load(open("PackageCategories.json", 'r'))
  generate_output_json_dict(pkgCatJson)
  with open("packages.json", 'w') as outputFile:
    json.dump(pkgCatJson, outputFile)

pkgNameSet = set()
pkgPosNamePrefixes = dict()
pkgNegNamePrefixes = dict()
pkgNameInterface = dict()
def generate_output_json_dict(pkgCatJson):
 # read package.csv file for more information
  packages_csv = csv.DictReader(open("Packages.csv",'r'))
  pkg = None
  for fields in packages_csv:
    if fields['Directory Name']:
      pkg = fields['Directory Name']
      pkgNameSet.add(pkg)
      pkgPosNamePrefixes[pkg] = []
      pkgNegNamePrefixes[pkg] = []
    if pkg and fields['Prefixes']:
      if fields['Prefixes'][0] == '!':
        pkgNegNamePrefixes[pkg].append(fields['Prefixes'][1:])
      else:
        pkgPosNamePrefixes[pkg].append(fields['Prefixes'])
      
  # read packageInterfaces.csv file for interface
  interface_csv = csv.DictReader(open("PackageInterface.csv", 'r'))
  for row in interface_csv:
    pkgName = row['Package']
    if pkgName and pkgName in pkgNameSet:
      if row['RPC']:
        pkgNameInterface.setdefault(pkgName,[]).append('RPC')
      if row['HL7']:
        pkgNameInterface.setdefault(pkgName,[]).append('HL7')
  
  traverseChildren(pkgCatJson)

def traverseChildren(package):
  if "children" in package:
    for child in package['children']:
      traverseChildren(child)
  else:
    pkgName = package['name']
    package['hasLink'] = pkgName in pkgNameSet
    if pkgName in pkgNameSet:
      package['Posprefixes'] = pkgPosNamePrefixes[pkgName]
      package['Negprefixes'] = pkgNegNamePrefixes[pkgName]
    if pkgName in pkgNameInterface:
      package['interfaces'] = pkgNameInterface[pkgName]

def main():
  generate_packages_json()

if __name__ == '__main__':
  main()
