#!/usr/bin/env php
<?php

/**
 * Usage: php utils/dump.php
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

// Real path of this script...
const TARGET = __DIR__ . '/../dumps/dump.sql';


$cmd = sprintf(
    'docker-compose exec %s mysqldump -u%s -p%s %s',
    CONTAINER,
    USER,
    PASSWORD,
    DB_NAME
);


$sql = shell_exec($cmd);

$lines = explode("\n", $sql);

$result = implode("\n", array_filter($lines, function (string $line) {
    foreach (TO_SKIP as $ban) {
        if (strpos($line, $ban) === 0) return false;
    }

    return true;
}));

file_put_contents(TARGET, $result);
