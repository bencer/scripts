#!/usr/bin/env python

from __future__ import print_function

import os
import sys
import json
import urllib
import urllib2
from hashlib import md5
import numpy

sys.path.insert(0,os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'bluepy')))
import btle

devices = ( {'name': 'charol', 'addr': 'DE:AD:BE:EF:00:01', 'values': [] },
            {'name': 'oliver', 'addr': 'DE:AD:BE:EF:00:02', 'values': [] })

sd_key = ''

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

def sd_postback(data):
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'text/html, */*'
    }

    payload_data = {
            'os': 'linux',
            'agentKey': sd_key,
            'agentVersion': '2.0'
    }

    payload_data['plugins'] = data
    payload_raw = json.dumps(payload_data, encoding='latin1').encode('utf-8')
    payload_hash = md5(payload_raw).hexdigest()
    body = urllib.urlencode(
            {
                'payload': payload_raw,
                'hash': payload_hash
            }
    )
    url = 'https://bencer.serverdensity.io/postback/'
    req = urllib2.Request(url, body, headers)
    try:
        f = urllib2.urlopen(req, timeout = 15)
        response = f.read()
        f.close()
    except urllib2.HTTPError as error:
        pass
    except urllib2.URLError as error:
        pass

def report():
    data = {} 
    data['CatSpy'] = {}
    for d in devices:
        list = d['values']
        name = d['name']+'_litter'
        if list == []:
            data['CatSpy'][name] = 0
            print("DEBUG {} {}".format(name, list))
        else:
            # percentile85 has given good results to filter out cat/signal movement
            percentile = numpy.percentile(numpy.array(list), 85)
            # we reset values
            d['values'] = []
            # -60 is the rssi thershold we consider in place
            if percentile < -60:
                data['CatSpy'][name] = 1
            if percentile >= -60:
                data['CatSpy'][name] = 2
            print("DEBUG {} {} {}".format(name, percentile, list))
    #from pprint import pprint
    #pprint(data)
    sd_postback(data)

if __name__ == "__main__":
    scanner = btle.Scanner().withDelegate(ScanReport2SD())
    print ("Scanning for devices...")
    while True:
        scanner.scan(20)
        report()
