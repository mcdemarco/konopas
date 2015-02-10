#!/bin/sh

ts=`date '+%Y-%m-%d_%H:%M'`
cm="konopas.appcache"
tgt="data/program.js"
tgtp="data/participants.js"
tm="$tgt.$ts"
tmp="$tgtp.$ts"

grenadine_prog_url="https://boskone52.grenadine.co/konopas/program.js"
grenadine_part_url="https://boskone52.grenadine.co/konopas/program/participants.js"

cd /home/nesfa/nesfa.org/boskone/konopas/

curl "$grenadine_prog_url" > "$tm" 2>/dev/null
curl "$grenadine_part_url" > "$tmp" 2>/dev/null

if  ! diff "$tm" "$tgt" >/dev/null 2>&1  ||  ! diff "$tmp" "$tgtp" >/dev/null 2>&1 
then
	cp "$tm" "$tgt"
	cp "$tmp" "$tgtp"
	d=`date`
	sed -i "s/^# .*/# $d/" $cm 2>/dev/null
else
	rm "$tm" "$tmp"
fi