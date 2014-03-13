import csv
import json

DISTR_LIST = ("VA","OSEHRA","DSS","Medsphere","Oroville")

def convert_pkg_cat_csv_json():
  pkg_catagory = csv.DictReader(open("ProductDefinition.csv",'r'))
  pkgCatDict = dict() # create a package info direct from csv file
  catPkgDict = dict() # store the Cat, Dict
  curCat = None
  curCap = None
  for fields in pkg_catagory:
    pkgCat = fields['Category']
    if pkgCat:
      curCat = pkgCat
      if curCat not in catPkgDict:
        catPkgDict[curCat] = dict()
      continue
    pkgCap = fields['Capacity']
    if pkgCap:
      curCap = pkgCap
      if curCap not in catPkgDict[curCat]:
        catPkgDict[curCat][curCap] = set()
      continue
    pkgName = fields['Package Name']
    if pkgName:
      if curCat and curCap:
        catPkgDict[curCat][curCap].add(pkgName)
      else:
        print "error"
      pkgCatDict[pkgName] = [fields[x] for x in DISTR_LIST]

  packageJson = generate_output_json_dict(catPkgDict, pkgCatDict)
  with open("packages.json", 'w') as outputFile:
    json.dump(packageJson, outputFile)

def generate_output_json_dict(inputDict, pkgCatDict):
 # read package.csv file for more information
  packages_csv = csv.DictReader(open("Packages.csv",'r'))
  pkgNameSet = set()
  pkgNamePrex = dict()
  pkgNameInterface = dict()
  pkg = None
  for fields in packages_csv:
    if fields['Directory Name']:
      pkg = fields['Directory Name']
      pkgNameSet.add(pkg)
      pkgNamePrex[pkg] = []
    if pkg and fields['Prefixes']:
      pkgNamePrex[pkg].append(fields['Prefixes'])
  # read packageInterfaces.csv file for interface
  interface_csv = csv.DictReader(open("PackageInterface.csv", 'r'))
  pkgNameInterface = dict()
  for row in interface_csv:
    pkgName = row['Package']
    if pkgName and pkgName in pkgNameSet:
      if row['RPC']:
        pkgNameInterface.setdefault(pkgName,[]).append('RPC')
      if row['HL7']:
        pkgNameInterface.setdefault(pkgName,[]).append('HL7')

  packageJson = dict() # the final json structure and convert to json file
  packageJson["name"] = "VistA"
  packageJson["children"] = []

  for key, value in inputDict.iteritems():
    outItem = dict()
    outItem['name'] = key
    if value and len(value) > 0:
      outItem['children'] = []
      for key2, val2 in value.iteritems():
        outCapItem = dict()
        outCapItem['name'] = key2
        if val2 and len(val2) > 0:
          outCapItem['children'] = []
          for pkg in val2:
            outPkg = dict()
            outPkg['name'] = pkg
            if pkg in pkgNameSet:
              outPkg['hasLink'] = True
              outPkg['prefixes'] = pkgNamePrex[pkg]
              if pkg in pkgNameInterface:
                outPkg['interfaces'] = pkgNameInterface[pkg]
            else:
              outPkg['hasLink'] = False
            outPkg['category'] = []
            infoLst = pkgCatDict[pkg]
            for idx, item in enumerate(infoLst):
              if item:
                outPkg['category'].append(DISTR_LIST[idx] + " Packages")
            outCapItem['children'].append(outPkg)
        outItem['children'].append(outCapItem)
    packageJson['children'].append(outItem)

  return packageJson

def main():
  convert_pkg_cat_csv_json()

if __name__ == '__main__':
  main()
