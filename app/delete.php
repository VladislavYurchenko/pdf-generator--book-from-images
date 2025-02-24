<?php
foreach (glob("uploads/*") as $file) unlink($file);
?>