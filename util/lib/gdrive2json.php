<?php

/*  Google Drive Spreadsheet -> JSON converter
 *  Copyright (c) 2013 by Eemeli Aro <eemeli@gmail.com>
 *
 *
 *  Fetches a non-private Google Drive/Docs spreadsheet as CSV and converts it
 *  to JSON. In field names, a "." indicates sub-objects; use zero-indexed
 *  entries to generate arrays rather than objects.
 *
 *  EXAMPLE USAGE:

      <?php
      $key = preg_replace('/\W/', '', @$_GET['key']);
      $gid = isset($_GET['gid']) ? preg_replace('/\D/', '', $_GET['gid']) : '0';

      require_once('gdrive2json.php');

      header("Content-type: application/json; charset=UTF-8;");
      echo gdrive2json($key, $gid);

 *  Permission to use, copy, modify, and/or distribute this software for any
 *  purpose with or without fee is hereby granted, provided that the above
 *  copyright notice and this permission notice appear in all copies.
 *
 *  The software is provided "as is" and the author disclaims all warranties
 *  with regard to this software including all implied warranties of
 *  merchantability and fitness. In no event shall the author be liable for
 *  any special, direct, indirect, or consequential damages or any damages
 *  whatsoever resulting from loss of use, data or profits, whether in an
 *  action of contract, negligence or other tortious action, arising out of
 *  or in connection with the use or performance of this software.
 *
 */


require_once('url_fetch.php');
require_once('parsecsv.lib.php');

function gdrive2json($key, $gid = '0') {
    if (!$key) exit("'key' parameter is required.");

    //$url = "https://docs.google.com/spreadsheets/d/$key/export?format=csv&id=$key&gid=404267302";
    //$url = "https://docs.google.com/spreadsheets/d/$key/gviz/tq?tqx=out:csv&sheet=404267302";
    $dest = array();
    $prog_item_id = 0;
    $all_people = array();
    $all_people_id = 0;
    $url_array = array();
    $url_array[] = "https://docs.google.com/spreadsheets/d/e/$key/pub?output=csv&gid=672691432";
    $url_array[] = "https://docs.google.com/spreadsheets/d/e/$key/pub?output=csv&gid=404267302";
    $url_array[] = "https://docs.google.com/spreadsheets/d/e/$key/pub?output=csv&gid=1067836657";
    foreach ($url_array as $url) {

        $rc = url_fetch($url, $csv_str);
        if ($rc) exit("URL fetch error: $rc");
    
        $csv = new parseCSV("$csv_str\n");
        $a = $csv->data;
        foreach ($a as $source_row) {
            if ($source_row['Day'] != '') {
                $dest_row = array();
                $pid = $prog_item_id++;
                $dest_row['id'] = $pid;
                $dest_row['title'] = $source_row['Title [All Times Eastern Daylight Time]'];
                $dest_row['desc'] = isset($source_row['Panel Description']) ? $source_row['Panel Description'] : $source_row['Event Description'];
                $dest_row['loc'] = array($source_row['Room']);
                $people_names = array();
                $moderator = $source_row['Moderator'];
                $people = array();
                if ($moderator != '' && substr($moderator, 1, 1) != '(' && $moderator != 'Precorded') {
                    $moderators = explode(',', $moderator);
                    foreach ($moderators as $mod) {
                        $mod2 = trim($mod);
                        if (isset($all_people[$mod2])) {
                            $all_people[$mod2]['prog'] = $pid;
                        } else {
                            $all_people[$mod2] = array('id' => $all_people_id++, 'prog' => array($pid));
                        }
                        $people[] = array('id' => $all_people[$mod2]['id'], 'name' => $mod2.' (moderator)');
                    }
                }
                $participant = $source_row['Panelists / Presenters'];
                if ($participant != '') {
                    $participants = explode(',', $participant);
                    foreach ($participants as $part) {
                        $part2 = trim($part);
                        if (isset($all_people[$part2])) {
                            $all_people[$part2]['prog'] = $pid;
                        } else {
                            $all_people[$part2] = array('id' => $all_people_id++, 'prog' => array($pid));
                        }
                        $people[] = array('id' => $all_people[$part2]['id'], 'name' => $part2);
                    }
                }
                if (count($people) > 0) {
                    $dest_row['people'] = $people;
                }
                switch ($source_row['Day']) {
                    case 'FRIDAY':
                        $date = '2020-08-21';
                        break;
                    case 'SATURDAY':
                        $date = '2020-08-22';
                        break;
                    case 'SUNDAY':
                        $date = '2020-08-23';
                        break;
                    default:
                        $date = '';
                }
                if ($date != '') {
                    $startDateTime = new DateTime($date.' '.$source_row['Start']);
                    $endDateTime = new DateTime($date.' '.$source_row['End']);
                    $dest_row['time'] = date_format($startDateTime, "H:i");
                    $dest_row['mins'] = $startDateTime->diff($endDateTime)->i;
                }
                $dest_row['date'] = $date;
                $dest[] = $dest_row;
            }
        }
    }
    $programjs = json_encode($dest);
    $dest_people = array();
    foreach ($all_people as $name => $person) {
        $dest_people[] = array('id' => $person['id'], 'name' => $name, 'prog' => $person['prog']);
    }
    $peoplejs = json_encode($dest_people);
	return array('program' => $programjs, 'people' => $peoplejs);
}
