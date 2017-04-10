import sys
import json
import urllib2
url = "https://api.github.com/repos/fabbaena/NetPivot/branches/" + sys.argv[1]
response = urllib2.urlopen(url)
data = response.read()
values = json.loads(data)
print values['commit']['sha']
