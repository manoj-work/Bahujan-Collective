<?php

namespace App\Livewire;

use App\Services\EventImporter;
use App\Services\OrganizationImporter;
use App\Support\CsvReader;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ImportData extends Component
{
    use WithFileUploads;

    public $eventsFile = null;

    public $organizationsFile = null;

    public ?array $eventResult = null;

    public ?array $organizationResult = null;

    protected $validationAttributes = [
        'eventsFile' => 'events CSV',
        'organizationsFile' => 'organizations CSV',
    ];

    public function updatedEventsFile(): void
    {
        $this->eventResult = null;
        $this->validateSelectedFile('eventsFile', ['event_name', 'name', 'title'], 'EVENT_NAME');
    }

    public function updatedOrganizationsFile(): void
    {
        $this->organizationResult = null;
        $this->validateSelectedFile('organizationsFile', ['org_name', 'organization', 'name'], 'ORG_NAME');
    }

    public function clearEventsFile(): void
    {
        $this->reset('eventsFile', 'eventResult');
        $this->resetValidation('eventsFile');
    }

    public function clearOrganizationsFile(): void
    {
        $this->reset('organizationsFile', 'organizationResult');
        $this->resetValidation('organizationsFile');
    }

    public function importEvents(EventImporter $importer): void
    {
        $this->validate($this->eventFileRules());
        $this->eventResult = $importer->importUploaded($this->eventsFile);
        $this->reset('eventsFile');
    }

    public function importOrganizations(OrganizationImporter $importer): void
    {
        $this->validate($this->organizationFileRules());
        $this->organizationResult = $importer->importUploaded($this->organizationsFile);
        $this->reset('organizationsFile');
    }

    public function render()
    {
        return view('livewire.import-data')
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Import · Bahujan Collective',
            ]);
    }

    protected function eventFileRules(): array
    {
        return [
            'eventsFile' => $this->csvRules(['event_name', 'name', 'title'], 'EVENT_NAME'),
        ];
    }

    protected function organizationFileRules(): array
    {
        return [
            'organizationsFile' => $this->csvRules(['org_name', 'organization', 'name'], 'ORG_NAME'),
        ];
    }

    protected function csvRules(array $requiredHeaders, string $label): array
    {
        return [
            'required',
            'file',
            'max:4096',
            function (string $attribute, mixed $value, \Closure $fail) use ($requiredHeaders, $label) {
                if (! $value instanceof TemporaryUploadedFile && ! $value instanceof \Illuminate\Http\UploadedFile) {
                    $fail('Please choose a CSV file.');
                    return;
                }

                $extension = strtolower((string) $value->getClientOriginalExtension());
                if (! in_array($extension, ['csv', 'txt'], true)) {
                    $fail('Only .csv files are allowed.');
                    return;
                }

                $path = $value->getRealPath();
                if (! $path) {
                    $fail('The CSV file could not be read.');
                    return;
                }

                $parsed = CsvReader::read($path);
                $headers = $parsed['headers'];
                $hasRequired = false;
                foreach ($requiredHeaders as $header) {
                    if (in_array($header, $headers, true)) {
                        $hasRequired = true;
                        break;
                    }
                }

                if (! $hasRequired) {
                    $fail("This CSV must include a {$label} column.");
                    return;
                }

                if ($parsed['rows'] === []) {
                    $fail('This CSV has a header row but no data rows.');
                }
            },
        ];
    }

    protected function validateSelectedFile(string $field, array $requiredHeaders, string $label): void
    {
        $this->validateOnly(
            $field,
            [$field => $this->csvRules($requiredHeaders, $label)],
            $this->messages()
        );
    }

    protected function messages(): array
    {
        return [
            'eventsFile.required' => 'Please choose an events CSV file.',
            'eventsFile.file' => 'Please choose a valid events CSV file.',
            'eventsFile.max' => 'The events CSV must be 4 MB or smaller.',
            'organizationsFile.required' => 'Please choose an organizations CSV file.',
            'organizationsFile.file' => 'Please choose a valid organizations CSV file.',
            'organizationsFile.max' => 'The organizations CSV must be 4 MB or smaller.',
        ];
    }
}
