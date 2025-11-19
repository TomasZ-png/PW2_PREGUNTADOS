<?php

function loadEnv($path)
{
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        if (substr($line, 0, 1) === '#') continue; // comentarios

        list($key, $value) = explode('=', $line, 2);

        // Cargarlo en PHP
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}