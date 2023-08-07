<?php

namespace TranslationSort;

/**
 * Class FileProcessor
 *
 * @package   TranslationSort
 */
class FileProcessor
{
    /**
     * This is just an example. The number set is just for dev testing purposes.
     * Usually, the number should be set accordingly to memory availability.
     */
    private const BATCH_SIZE = 3;

    private $file;

    /**
     * @param string $filename
     * @return array contains all temporary batch file names into which the initial file was split.
     */
    public function execute(string $filename): array
    {
        $this->file = fopen($filename, 'r');

        while (!feof($this->file)) {
            $batch = $this->extractBatch();

            // Since we have batches that fit into memory, we sort them.
            ksort($batch);

            // Each sorted batch will be saved to a temporary file.
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

    /**
     * Reads the input from the file line by line, preserving comments above
     * each translation line. I assumed that we are working with files that
     * don't fit into memory, so each file will be split into batches. The size
     * of each batch is configurable.
     *
     * @return array
     */
    private function extractBatch(): array
    {
        $batch = [];
        $comments = [];

        while (count($batch) < self::BATCH_SIZE && ($line = fgets($this->file)) !== false) {
            if (str_starts_with($line, '#')) {
                $comments[] = $line;
                continue;
            }

            // Here we could add some validation to ensure the line has a correct format.
            // The reason why I didn't implement this is that there is no specific format defined
            // besides key=value.
            // If given a set of rules, my approach would be to have a ValidatorInterface and a
            // validator pool (this way we can add new validation rules easily). All concrete classes
            // in the validator pool will implement ValidatorInterface.

            list($key, $value) = explode('=', $line, 2);
            $batch[$key] = ['value' => $line, 'comments' => $comments];
            $comments = [];
        }

        return $batch;
    }
}