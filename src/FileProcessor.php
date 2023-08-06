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
            ksort($batch);

            // save sorted batch to temp file
            $temporaryFilename = tempnam(sys_get_temp_dir(), 'sorted_batch_');

            foreach ($batch as $batchLine) {
                file_put_contents($temporaryFilename, $batchLine['comments'], FILE_APPEND);
                file_put_contents($temporaryFilename, $batchLine['value'], FILE_APPEND);
            }

            $temporaryFiles[] = $temporaryFilename;
        }

        fclose($this->file);

        return $temporaryFiles;
    }

    private function extractBatch(): array
    {
        $batch = [];
        $comments = [];

        while (count($batch) < self::BATCH_SIZE && ($line = fgets($this->file)) !== false) {
            if (str_starts_with($line, '#')) {
                $comments[] = $line;
                continue;
            }

            // TODO: validate

            list($key, $value) = explode('=', $line, 2);
            $batch[$key] = ['value' => $line, 'comments' => $comments];
            $comments = [];
        }

        return $batch;
    }
}