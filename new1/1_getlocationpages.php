<?php
$url = "http://www.urbanspoon.com/choose";
$file = file_get_contents($url);
file_put_contents("cities/all.html", $file);
echo 'done';
?>