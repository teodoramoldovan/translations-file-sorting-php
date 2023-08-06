<?php

namespace TranslationSort;

use SplMinHeap;

class Sorter
{
    private const OUTPUT_FILE_NAME = 'sorted.properties';
    public function mergeSortedFiles(array $filePaths)
    {
        $outputFile = fopen(self::OUTPUT_FILE_NAME, 'w');

        $inputFiles = [];

        foreach ($filePaths as $filePath) {
            $inputFiles[] = fopen($filePath, 'r');
        }

        $heap = new SplMinHeap();

        $comments = [];

        // initialize heap with initial values from each file
        foreach ($inputFiles as $fileIndex => $file) {
            $line = fgets($file);

            while (str_starts_with($line, '#')) {
                $comments[] = $line;
                $line = fgets($file);
            }

            if ($line !== false) {
                $heap->insert([$line, $comments, $fileIndex]);
                $comments = [];
            }
        }

        $nextLineComments = [];

        while (!$heap->isEmpty()) {
            [$value, $comments, $fileIndex] = $heap->extract();

            foreach ($comments as $comment) {
                fwrite($outputFile, $comment);
            }

            file_put_contents(self::OUTPUT_FILE_NAME, $comments, FILE_APPEND);
            fwrite($outputFile, $value);

            $nextLine = fgets($inputFiles[$fileIndex]);

            while (str_starts_with($nextLine, '#')) {
                $nextLineComments[] = $nextLine;
                $nextLine = fgets($inputFiles[$fileIndex]);
            }

            if ($nextLine !== false) {
                $heap->insert([$nextLine, $nextLineComments, $fileIndex]);
                $nextLineComments = [];
            }
        }

        fclose($outputFile);
        foreach ($inputFiles as $file) {
            fclose($file);
        }
    }

}