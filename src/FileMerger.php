<?php

namespace TranslationSort;

use SplMinHeap;

/**
 * Class FileMerger
 *
 * @package   TranslationSort
 */
class FileMerger
{
    private const OUTPUT_FILE_NAME = 'sorted.properties';

    /**
     * @param array $filePaths
     * @return void
     */
    public function mergeSortedFiles(array $filePaths): void
    {
        $outputFile = fopen(self::OUTPUT_FILE_NAME, 'w');

        // Since the reading will be done line by line, we open all files at once.
        $inputFiles = $this->openFiles($filePaths);

        // Create a new SplMinHeap and initialize it with the first translation line from each
        // file, while preserving the comments associated to it (if any)
        $heap = $this->initializeHeap($inputFiles);

        while (!$heap->isEmpty()) {
            [$value, $comments, $fileIndex] = $heap->extract();

            foreach ($comments as $comment) {
                fwrite($outputFile, $comment);
            }
            fwrite($outputFile, $value);

            $this->addTranslationToHeap($heap, $inputFiles[$fileIndex], $fileIndex);
        }

        fclose($outputFile);

        foreach ($inputFiles as $file) {
            fclose($file);
        }
    }

    /**
     * @param array $filePaths
     * @return array contains opened file streams.
     */
    private function openFiles(array $filePaths): array
    {
        $inputFiles = [];

        foreach ($filePaths as $filePath) {
            $inputFiles[] = fopen($filePath, 'r');
        }

        return $inputFiles;
    }

    /**
     * Having sorted inputs, we use a minHeap to ensure the alphabetical order is preserved
     * when merging the files.
     *
     * @param array $inputFiles
     * @return SplMinHeap
     */
    private function initializeHeap(array $inputFiles): SplMinHeap
    {
        $heap = new SplMinHeap();

        foreach ($inputFiles as $fileIndex => $file) {
            $this->addTranslationToHeap($heap, $file, $fileIndex);
        }

        return $heap;
    }

    /**
     * Adds a new element to the heap, including the translation and the associated comments.
     * @param SplMinHeap $heap
     * @param $file
     * @param $fileIndex
     * @return void
     */
    private function addTranslationToHeap(SplMinHeap $heap, $file, $fileIndex): void
    {
        $line = fgets($file);

        $comments = [];

        while (str_starts_with($line, '#')) {
            $comments[] = $line;
            $line = fgets($file);
        }

        if ($line !== false) {
            $heap->insert([$line, $comments, $fileIndex]);
        }
    }
}