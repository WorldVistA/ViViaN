import csv
import os
import glob

def run():
  outputFile = "PackageInterface.csv"
  pkgRPCFile = glob.glob("files/8994/*-RPC.html")
  pkgHL7File = glob.glob("files/101/*-HL7.html")
  pkgProtocolFile = glob.glob("files/101/*-Protocols.html")
  pkgHLOFile = glob.glob("files/779_2/*-HLO.html")
  pkgICRFile = glob.glob("files/ICR/*-ICR.html")
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


if __name__ == '__main__':
  run()
