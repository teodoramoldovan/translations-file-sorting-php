<?php

namespace TranslationSort;

class FileProcessor
{
    private const BATCH_SIZE = 3;

    private $file;

    public function __construct(
        private readonly string $filename
    ) {
        // initialize the stream here so that we can resume processing from the point we were left in
        $this->file = fopen($filename, 'r');
    }

    public function execute()
    {
        while (!feof($this->file)) {
            $batch = $this->extractBatch();

            // TODO validate batch

            // sort validated batch
            sort($batch);

            // save sorted batch to temp file
            $temporaryFilename = tempnam(sys_get_temp_dir(), 'sorted_batch_');
            file_put_contents($temporaryFilename, $batch);
            $temporaryFiles[] = $temporaryFilename;

            echo PHP_EOL;
        }

        return $temporaryFiles;
    }

    private function extractBatch(): array
    {
        $batch = [];

        while (count($batch) < self::BATCH_SIZE && ($line = fgets($this->file)) !== false) {
            $batch[] = $line;
        }

        return $batch;
    }
}