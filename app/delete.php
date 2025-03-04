<?php
$root = '/var/www/html/storage';

foreach (glob("$root/output/*") as $file) unlink($file);
?>