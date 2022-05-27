<?php

$old = 'localhost:8080';
$new = $argv[1] ?? $old;

$sql = file_get_contents('dumps/dump.sql');
$sql = preg_replace("/$old/", $new, $sql);

$dump = 'dumps/z-dump-'. time() .'.sql';

file_put_contents($dump, $sql);

`docker-compose up -d`;
`docker-compose exec ubuntu bash wait-for-it.sh db:3306`;

unlink($dump);

// $sql = '';
// $handle = fopen('utils/deploy.php', 'rt');
// file_get_contents()
// while (($line = fgets($handle)) !== false) {
//     if (mb_strpos($line, $old) !== false) {
//         $sql .= preg_replace("/$old/u", $new, $line);
//     } else {
//         $sql .= $line;
//     }
// }
// fclose($handle);
