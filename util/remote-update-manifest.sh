#!/bin/sh

ts=`date '+%Y-%m-%d_%H:%M'`
cm="konopas.appcache"
tgt="data/program.js"
tgtp="data/participants.js"
tm="$tgt.$ts"
tmp="$tgtp.$ts"

grenadine_prog_url="https://mac2.grenadine.co/konopas/program.js"
grenadine_peep_url="https://mac2.grenadine.co/konopas/program/participants.js"

cd /var/www/html/

curl "$grenadine_prog_url" > "$tm" 2>/dev/null
curl "$grenadine_peep_url" > "$tmp" 2>/dev/null

cp "$tm" "$tgt"
cp "$tmp" "$tgtp"
d=`date`
sed -i "s/^# .*/# $d/" $cm 2>/dev/null
