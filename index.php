<?php
/**
 * In order to avoid premature optimization, I decided to go for a plain php solution,
 * using only concrete classes in order to solve the tasks.
 */

use TranslationSort\FileProcessor;
use TranslationSort\FileMerger;

require_once 'vendor/autoload.php';

$filename = 'unsorted-translations.properties';

try {
    $fileProcessor = new FileProcessor();
    $temporaryFiles = $fileProcessor->execute($filename);

    $sorter = new FileMerger();
    $sorter->mergeSortedFiles($temporaryFiles);
} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage();
}