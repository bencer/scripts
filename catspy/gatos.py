#!/usr/bin/env python

from __future__ import print_function

import os
import sys
import json
import numpy

sys.path.insert(0,os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'bluepy')))
import btle

devices = ( {'name': 'charol', 'addr': 'DE:AD:BE:EF:00:01', 'values': [] },
            {'name': 'oliver', 'addr': 'DE:AD:BE:EF:00:02', 'values': [] })

class ScanReport2SD(btle.DefaultDelegate):
    def handleDiscovery(self, dev, isNewDev, isNewData):
        if dev.rssi < -128:
            return
      
        for d in devices:
            # quick and dirty
            try:
                if d['addr'] == dev.addr:
                    d['values'].append(dev.rssi)
            except:
                continue

def report():
    data = {} 
    for d in devices:
        list = d['values']
        name = d['name']+'_litter'
        if list == []:
            data[name] = 0
            print("DEBUG {} {}".format(name, list))
        else:
            # percentile85 has given good results to filter out cat/signal movement
            percentile = numpy.percentile(numpy.array(list), 85)
            # we reset values
            d['values'] = []
            # -60 is the rssi thershold we consider in place
            if percentile < -60:
                data[name] = 1
            if percentile >= -60:
                data[name] = 2
            print("DEBUG {} {} {}".format(name, percentile, list))
    from pprint import pprint
    pprint(data)
    f = open('/tmp/CatSpy.json', 'w')
    f.write(json.dumps(data))
    f.close()

if __name__ == "__main__":
    scanner = btle.Scanner().withDelegate(ScanReport2SD())
    print ("Scanning for devices...")
    while True:
        scanner.scan(20)
        report()
