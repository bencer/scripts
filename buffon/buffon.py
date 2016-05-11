import os
import sys
sys.path.append('./.local/lib/python2.7/site-packages/')

import getopt
from datetime import datetime, timedelta

import bitly_api
from buffpy.managers.profiles import Profiles
from buffpy.managers.updates import Updates
from buffpy.api import API

BITLY_ACCESS_TOKEN = ""
BUFFER_ACCESS_TOKEN = ""


def get_bitly_connection():
    bitly = bitly_api.Connection(access_token=BITLY_ACCESS_TOKEN)
    return bitly


def get_bitly(url):
    bitly = get_bitly_connection()
    data = bitly.shorten(url)
    return data['url']


def gen_url(slug, campaign, social):
    url = "https://blog.serverdensity.com/%s/?utm_campaign=%s&utm_medium=social&utm_source=%s" % (slug, campaign, social)
    return url


def get_buffer_connection():
    api = API(client_id='',
          client_secret='',
          access_token=BUFFER_ACCESS_TOKEN)
    return api


def post_buffer(msg, url, social):
    bufferapp = get_buffer_connection()
    profile = Profiles(api=bufferapp).filter(service=social)[0]
    post_time = datetime.utcnow() + timedelta(minutes=180)
    post_msg = "%s - %s" % (msg, url)
    profile.updates.new(post_msg, when=post_time)


def main(argv):
    slug = ''
    campaign = ''
    msg = ''
    date = ''
    try:
        opts, args = getopt.getopt(argv,"hs:c:m:d:",["slug=","campaign=","msg=","date="])
    except getopt.GetoptError:
        print 'buffon.py -s|--slug <slug> -c|--campaign <campaign> -m|--msg <msg> -d|--date <date>'
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            print 'buffon.py -s|--slug <slug> -c|--campaign <campaign> -m|--msg <msg> -d|--date <date>'
            sys.exit()
        elif opt in ("-s", "--slug"):
            slug = arg
        elif opt in ("-c", "--campaign"):
            campaign = arg
        elif opt in ("-m", "--msg"):
            msg = arg
        elif opt in ("-d", "--date"):
            date = arg
    for social in ['twitter', 'facebook', 'google']:
        url = gen_url(slug, campaign, social)
        short_url = get_bitly(url)
        print "%s: %s - %s" % (social, msg, short_url)
        post_buffer(msg, short_url, social)


if __name__ == '__main__':
    main(sys.argv[1:])
