<?php

/**
 * Usage: php utils/dump.php > dump.sql
 * @todo parametrize
 */

define('BAN_LINE', 'mysqldump:');

define('CONTAINER', 'db'); // Docker COmpose container name
define('USER', 'exampleuser'); // Database user
define('PASSWORD', 'examplepass'); // Database password
define('DB_NAME', 'exampledb');


$cmd = sprintf(
    'docker-compose exec %s mysqldump -u%s -p%s %s',
    CONTAINER,
    USER,
    PASSWORD,
    DB_NAME
);

$sql = shell_exec($cmd);

echo implode(
    "\n",
    array_filter(
        explode("\n", $sql),
        fn (string $line) => !str_starts_with($line, BAN_LINE)
    )
);
