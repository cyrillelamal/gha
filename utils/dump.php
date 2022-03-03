<?php

/**
 * Usage: php utils/dump.php > dump.sql
 * @todo parametrize
 */

const TO_SKIP = [
    'mysqldump:',
    'Unable to close the console',
    'failed to get console mode for stdout',
];

const CONTAINER = 'db'; // Docker Compose container name
const USER = 'exampleuser'; // Database user
const PASSWORD = 'examplepass'; // Database password
const DB_NAME = 'exampledb';


$cmd = sprintf(
    'docker-compose exec %s mysqldump -u%s -p%s %s',
    CONTAINER,
    USER,
    PASSWORD,
    DB_NAME
);

$sql = shell_exec($cmd);

$lines = explode("\n", $sql);

echo implode("\n", array_filter($lines, function (string $line) {
    foreach (TO_SKIP as $ban) {
        if (str_starts_with($line, $ban)) return false;
    }

    return true;
}));
