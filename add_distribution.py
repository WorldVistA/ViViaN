import json
def main():
  with open("PackageCategories.json", "r") as input:
    inputJson = json.load(input)
    addDistribution(inputJson) 
    with open("PackageCategories-0.json", "w") as output:
      json.dump(inputJson, output, indent=4)

def addDistribution(treeNode):
  if 'children' in treeNode:
    for child in treeNode['children']:
      addDistribution(child)
  else:
    if 'distribution' not in treeNode:
      treeNode['distribution'] = ['OSEHRA', 'VA', 'DSS'];

if __name__ == '__main__':
  main()
