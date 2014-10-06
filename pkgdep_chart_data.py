import json

def main():
  pkgdepjson = json.load(open('pkgdep.json', 'rb'))
  pkgs = {}
  for pkg in pkgdepjson:
    if 'depends' in pkg:
      pkgs.setdefault(pkg['name'],{})['depends'] = pkg['depends']
    if 'routines' in pkg:
      pkgs.setdefault(pkg['name'],{})['routines'] = pkg['routines']
    if 'files' in pkg:
      pkgs.setdefault(pkg['name'],{})['files'] = pkg['files']
  for pkg in pkgdepjson:
    if 'depends' in pkg:
      for dep in pkg['depends']:
        if dep not in pkgs:
          print "%s not in pkgs" % dep
          pkgs.setdefault(dep,{})
        pkgs[dep].setdefault('dependents',[]).append(pkg['name'])
    
  import pprint
  pprint.pprint(pkgs)
  outJson = {}
  outJson['categories'] = sorted(pkgs.keys())
  outJson['series'] = [
                        {
                         'name': 'dependency',
                         'data': []
                        },
                        {
                         'name': 'dependents',
                         'data': []
                        },
                        {
                         'name': 'routines',
                         'data': []
                        },
                        {
                         'name': 'fileman files',
                         'data': []
                        }
                      ]
  
  for key in sorted(pkgs.keys()):
    print key
    if 'depends' in pkgs[key]:
      outJson['series'][0]['data'].append(len(pkgs[key]['depends']))
    else:
      outJson['series'][0]['data'].append(0)

    if 'dependents' in pkgs[key]:
      outJson['series'][1]['data'].append(len(pkgs[key]['dependents']))
    else:
      outJson['series'][1]['data'].append(0)

    if 'routines' in pkgs[key]:
      print len(pkgs[key]['routines'])
      outJson['series'][2]['data'].append(len(pkgs[key]['routines']))
    else:
      outJson['series'][2]['data'].append(0)

    if 'files' in pkgs[key]:
      print len(pkgs[key]['files'])
      outJson['series'][3]['data'].append(len(pkgs[key]['files']))
    else:
      outJson['series'][3]['data'].append(0)
    
  json.dump(outJson, open("pkg_dep_chart.json", 'w'), indent=4)

if __name__ == '__main__':
  main()
