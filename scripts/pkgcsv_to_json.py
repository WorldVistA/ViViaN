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

import csv
import json

DISTR_LIST = ("VA","OSEHRA","DSS","Medsphere","Oroville")

pkgNameSet = set()
pkgPosNamePrefixes = dict()
pkgNegNamePrefixes = dict()
pkgNameInterface = dict()
pkgPrefixDic = dict()
pkgAutocomple = set()

def run(filesDir):
  pkgCatJson = json.load(open("../PackageCategories.json", 'r'))
  pkgDesJson = json.load(open("PackageDes.json", 'r'))
  generate_output_json_dict(pkgCatJson, pkgDesJson, filesDir)
  packagesFilename = "%s/packages.json" % filesDir
  with open(packagesFilename, 'w') as outputFile:
    json.dump(pkgCatJson, outputFile)
    print("*** Updated %s" % packagesFilename)
  autocompleteFilename = "%s/packages_autocomplete.json" % filesDir
  with open(autocompleteFilename, 'w') as autocompleteOutputFile:
    pkgAutocomple.add("Unknown")
    packageNames = list(pkgAutocomple)
    packageNames.sort()
    json.dump(packageNames, autocompleteOutputFile)
    print("*** Updated %s" % autocompleteFilename)

def generate_output_json_dict(pkgCatJson, pkgDesJson, filesDir):
 # read package.csv file for more information
  packages_csv = csv.DictReader(open("../Packages.csv",'r'))
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
        pkgPrefixDic[fields['Prefixes']] = pkg

  # read packageInterfaces.csv file for interface
  interface_csv = csv.DictReader(open("%s/PackageInterface.csv" % filesDir, 'r'))
  for row in interface_csv:
    pkgName = row['Package']
    if pkgName and pkgName in pkgNameSet:
      if 'RPC' in row and row['RPC']:
        pkgNameInterface.setdefault(pkgName,[]).append('RPC')
      if 'HL7' in row and row['HL7']:
        pkgNameInterface.setdefault(pkgName,[]).append('HL7')
      if 'Protocols' in row and row['Protocols']:
        pkgNameInterface.setdefault(pkgName,[]).append('Protocols')
      if 'HLO' in row and row['HLO']:
        pkgNameInterface.setdefault(pkgName,[]).append('HLO')
      if 'ICR' in row and row['ICR']:
        pkgNameInterface.setdefault(pkgName,[]).append('ICR')

  traverseChildren(pkgCatJson, pkgDesJson)

def traverseChildren(package, pkgDesJson):
  if "children" in package:
    for child in package['children']:
      traverseChildren(child, pkgDesJson)
  else:
    pkgName = package['name']
    pkgAutocomple.add(pkgName);
    package['hasLink'] = pkgName in pkgNameSet
    if pkgName in pkgNameSet:
      package['Posprefixes'] = pkgPosNamePrefixes[pkgName]
      package['Negprefixes'] = pkgNegNamePrefixes[pkgName]
      for prefix in package['Posprefixes']:
        if prefix in pkgDesJson:
          if 'des' in pkgDesJson[prefix]:
            package['des'] = pkgDesJson[prefix]['des']
          else:
            package['des'] = pkgDesJson[prefix]['shortdes']
          break
    if pkgName in pkgNameInterface:
      package['interfaces'] = pkgNameInterface[pkgName]
