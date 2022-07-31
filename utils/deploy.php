<?php

$old = 'http://localhost:8080';
$new = $argv[1] ?? $old;

$sql = file_get_contents('/dumps/dump.sql');
$sql = preg_replace("|$old|u", $new, $sql);
echo $sql;
