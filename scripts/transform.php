<?php
// scripts/transform.php

declare(strict_types=1);

[$_, $inputDir, $outputDir] = $argv;

$files = glob($inputDir . '/*');

foreach ($files as $file) {
    $name = basename($file);
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $rules = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if (str_starts_with($line, '#') || str_starts_with($line, 'include:')) {
            continue;
        }

        $rules[] = match (true) {
            str_starts_with($line, 'domain:') => 'DOMAIN-SUFFIX,' . substr($line, 7),
            str_starts_with($line, 'full:') => 'DOMAIN,'        . substr($line, 5),
            str_starts_with($line, 'regexp:') => 'URL-REGEX,'     . substr($line, 7),
            default => 'DOMAIN-SUFFIX,' . $line,
        };
    }

    $rules = array_filter($rules);
    file_put_contents("$outputDir/$name.list", implode("\n", $rules) . "\n");
}

echo "Done: " . count($files) . " files processed\n";
