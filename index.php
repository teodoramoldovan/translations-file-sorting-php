<?php

use TranslationSort\FileProcessor;
use TranslationSort\Sorter;

require_once 'vendor/autoload.php';

$filename = 'unsorted-translations.properties';

try {
    $fileProcessor = new FileProcessor($filename);

    $tempFiles = $fileProcessor->execute();

    $sorter = new Sorter();
    $sorter->mergeSortedFiles($tempFiles);

    //TODO remove temporary files

} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage();
}