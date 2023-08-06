<?php

use TranslationSort\FileProcessor;

require_once 'vendor/autoload.php';

$filename = 'unsorted-translations.properties';

try {
    $fileProcessor = new FileProcessor($filename);

    $tempFiles = $fileProcessor->execute();

    foreach ($tempFiles as $tempFile) {
       echo $tempFile . PHP_EOL;
    }

} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage();
}