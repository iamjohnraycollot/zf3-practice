<?php

$db = new PDO('mysql:' . realpath(__DIR__) . '/zendframework3.db');

$fh = fopen(__DIR__ . '/schema.sql', 'r');
while ($line = fread($fh, 4096)) {
    $db->exec($line);
}

fclose($fh);