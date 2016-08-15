<?php
$message=shell_exec("/var/www/script/remote-update-manifest.sh 2>&1");
print_r($message);
?>
