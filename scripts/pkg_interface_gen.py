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
import os
import glob

def run(filesDir):
  outputFile = "%s/PackageInterface.csv" % filesDir
  pkgRPCFile = glob.glob("%s/8994/*-RPC.html" % filesDir)
  pkgHL7File = glob.glob("%s/101/*-HL7.html" % filesDir)
  pkgProtocolFile = glob.glob("%s/101/*-Protocols.html" % filesDir)
  pkgHLOFile = glob.glob("%s/779_2/*-HLO.html" % filesDir)
  pkgICRFile = glob.glob("%s/ICR/*-ICR.html" % filesDir)
  header = ['Package','RPC','HL7','HLO','Protocols', 'ICR']
  outCsv = {}
  for rpcFile in pkgRPCFile:
    if rpcFile.endswith("All-RPC.html"):
      continue
    rpcFile = os.path.basename(rpcFile)
    rpcFile =  rpcFile[:-9]
    outCsv.setdefault(rpcFile, [None, None, None, None, None])[0] = True
  for hl7File in pkgHL7File:
    if hl7File.endswith("All-HL7.html"):
      continue
    hl7File = os.path.basename(hl7File)
    hl7File =  hl7File[:-9]
    outCsv.setdefault(hl7File, [None, None, None, None, None])[1] = True

  for hlOFile in pkgHLOFile:
    if hlOFile.endswith("All-HLO.html"):
      continue
    hlOFile = os.path.basename(hlOFile)
    hlOFile =  hlOFile[:-9]
    outCsv.setdefault(hlOFile, [None, None, None, None, None])[2] = True

  for protocolFile in pkgProtocolFile:
    if protocolFile.endswith("All-Protocols.html"):
      continue
    protocolFile = os.path.basename(protocolFile)
    protocolFile =  protocolFile[:-15]
    outCsv.setdefault(protocolFile, [None, None, None, None, None])[3] = True

  for icrFile in pkgICRFile:
    if icrFile.endswith("All-ICR.html"):
      continue
    icrFile = os.path.basename(icrFile)
    icrFile =  icrFile[:-9]
    outCsv.setdefault(icrFile, [None, None, None, None, None])[4] = True

  with open(outputFile, 'w') as output:
    csvWtr = csv.writer(output, lineterminator='\n')
    csvWtr.writerow(header)
    for key in sorted(outCsv.keys()):
      outCsv[key].insert(0,key)
      csvWtr.writerow(outCsv[key])
  print("*** Updated %s" % outputFile)
