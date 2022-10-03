#!/bin/sh
[ -f update.lock ] && rm update.lock
while true; do
perl client.pl
if [ $? -ne 7 ]; then break; fi
done