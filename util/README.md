KonOpas Utilities
=================

All KonOpas utilities are distributed under the same ISC license as KonOpas itself.

For more online tools to help with KonOpas, see:  http://konopas.org/util/


KonOpas cache manifest updater
------------------------------

`update-cache-manifest.php` updates the timestamp on the `konopas.appcache` file in KonOpas root. That file is used by the HTML5 cache KonOpas uses to indicate changes in data. So if your programme gets updated, you'll want to either run `update_cache_manifest(...)` directly (as `read-from-gdrive.php` does), or do an HTTP GET request for this file.


Google Drive Spreadsheet -> KonOpas Javascript converter
--------------------------------------------------------

`read-from-gdrive.php` is a tool for using a Google Drive/Docs spreadsheet as a data source for KonOpas. To use, you'll need non-private spreadsheets for programme and people data, each with appropriate labels on the first row. See here for an example, from Finncon 2013:

https://docs.google.com/spreadsheet/ccc?key=0Auqwt8Hmhr0pdFRiR0hWWWRqRXVUSDVUY2RFYmRzZ0E

To use, modify the `$data` array in the PHP file to point to your spreadsheet's `key` and `gid`, and set `tgt` to point to the right path for the formatted data. In the source spreadsheet, field names are defined by the first row: a "." indicates sub-objects; use zero-indexed entries to generate arrays rather than objects. The number of sub-objects and array entries are not limited. Don't leave empty rows at the end of the sheets, and in arrays don't leave empty values in the middle.


Apache config / .htaccess file
--------------------------------------------

The included .htaccess file contains directives for several circumstances described below.  If you need it, you should edit it to include only the sections you need, rename it to `.htaccess`, and put it in the root directory from which you're serving KonOpas' `index.html` file.

### Serving the cache manifest file

Most Apache servers are already set up to serve your cache manifest file (`konopas.appcache`) correctly.  If browsers are not receiving this file, use the directive included.

### Switching to HTTPS

Since *some* browsers will fail to save the manifest under HTTP, it's a good idea to redirect all users to HTTPS.  If you don't have an SSL certificate for your website, you will need to get one.  Most hosting services provide an easy way for you to set up a free Let's Encrypt certificate.  Do that as soon as possible, since it can take some time.

If your users ever used KonOpas at the HTTP URL, a simple HTTPS redirect will be rejected and the cached data will continue to be used, even if it is a year out of date.  While it's possible to rename the manifest file to avoid this problem, that would require some rewriting of KonOpas' code (where the manifest is hard-coded), as well as editing the cache manifest updater if you're using it.  An easier solution is to redirect almost all traffic to HTTPS, but to return a 410 Gone error to a request for the manifest over HTTP.  The directives to do so are included in the file.

### Handling Chrome-specific bugs

Since *some* browsers think "deprecated" means "[FUBAR](https://bugs.chromium.org/p/chromium/issues/detail?id=899752)", you may find yourself in a situation where users are seeing your new program data served live (no caching!) within a past year's index file (no cache updates ever) under HTTP---possibly also without your fonts if you have switched to HTTPS.  Depending on how much you change your index file from year to year as well as the cell or wireless coverage of your venue, users may not even notice the bug.

If you can live with this situation, there is a directive in the file to fix the font issue alone.

There is no way for KonOpas to convince Chrome to stop using the cached index file, so to fix the situation, you will need to switch to HTTPS following the instructions above, move your program data to a new file, say, `program2019.js`, update all the relevant files (`index.html`, `konopas.appcache`, and any scripts you may be running to populate and/or update your program), and put a message into the old program file (say, `program.js`) telling them to use the new HTTPS url.

A sample `program.js` file for the purpose is included in this directory.  You should edit it very carefully to include your new URL in all places where it currently says `https://example.org`.
