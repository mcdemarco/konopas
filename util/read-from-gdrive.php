<?php

/*  Google Drive Spreadsheet -> KonOpas Javascript converter
 *  Copyright (c) 2013-2014 by Eemeli Aro <eemeli@gmail.com>
 *
 *
 *  A tool for using a Google Drive/Docs spreadsheet as a data source for
 *  KonOpas. To use, you'll need non-private spreadsheets for programme and
 *  people data, each with appropriate labels on the first row. See here for
 *  an example, from Finncon 2013:
 *
 *  https://docs.google.com/spreadsheet/ccc?key=0Auqwt8Hmhr0pdFRiR0hWWWRqRXVUSDVUY2RFYmRzZ0E
 *
 *  To use, modify the $data array below to point to your data.
 *
 *
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


$data = array(
	'program' => array(
		'key' => '2PACX-1vSiDrtz8-QSkA20XcURO-OoeYyX3CmEwMGFU-pOMioQQUz4R4dyf1-uIxLzAKUBaf8ucgXOqP168yNC',
		'gid' => '0',
		'tgt' => array('program' => '../data/program.js', 'people' => '../data/people.js')
	)
/*,
	'people' => array(
		'key' => '0Auqwt8Hmhr0pdFRiR0hWWWRqRXVUSDVUY2RFYmRzZ0E',
		'gid' => '1',
		'tgt' => '../data/finncon2013/people.js'
	)*/
);

$cache_manifest = '../konopas.appcache';  // set to FALSE to disable


// You should not need to change anything below this line.

require_once('lib/gdrive2json.php');
require_once('update-cache-manifest.php');

function gdrive2konopas($name, $set) {
	$json_array = gdrive2json($set['key'], $set['gid']);
	if (strlen($json_array['program']) == 0) exit("JSON length 0! Error!");
	foreach($set['tgt'] as $tgt_name => $tgt_file) {
        $js = "var $tgt_name = {$json_array[$tgt_name]};";
        $dir = dirname($tgt_file);
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        $write_len = file_put_contents($tgt_file, $js);
        if ($write_len != strlen($js)) exit("Write error! $write_len != " . strlen($js));
    }
}

header("Content-type: text/plain; charset=UTF-8;");
echo "Google Drive -> KonOpas\n=======================\n";
foreach ($data as $k => $v) {
	echo "\nUpdating $k data... "; flush();
	gdrive2konopas($k, $v);
	echo "ok.\n"; flush();
}
// echo "\n" . update_cache_manifest($cache_manifest);
echo "\nAll done.\n";
