<?php

namespace App\Console\Commands;

use App\Services\EventImporter;
use Illuminate\Console\Command;

class ImportEventsCommand extends Command
{
    protected $signature = 'import:events {file : Path to a CSV file}';

    protected $description = 'Import events from a CSV file';

    public function handle(EventImporter $importer): int
    {
        $file = $this->argument('file');
        if (! is_readable($file)) {
            $this->error("File not found or not readable: {$file}");
            return self::FAILURE;
        }

        $result = $importer->importPath($file);
        $this->info("Added {$result['imported']}, updated {$result['updated']}, skipped {$result['skipped']}.");

        foreach ($result['errors'] as $error) {
            $this->warn($error);
        }

        return self::SUCCESS;
    }
}
