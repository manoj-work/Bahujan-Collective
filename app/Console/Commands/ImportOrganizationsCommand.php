<?php

namespace App\Console\Commands;

use App\Services\OrganizationImporter;
use Illuminate\Console\Command;

class ImportOrganizationsCommand extends Command
{
    protected $signature = 'import:organizations {file : Path to a CSV file}';

    protected $description = 'Import organizations from a CSV file';

    public function handle(OrganizationImporter $importer): int
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
