<?php

namespace App\Services;

use App\Models\Organization;
use App\Support\CsvReader;
use Illuminate\Http\UploadedFile;

class OrganizationImporter
{
    public function importPath(string $path): array
    {
        return $this->importRows(CsvReader::read($path)['rows']);
    }

    public function importUploaded(UploadedFile $file): array
    {
        return $this->importRows(CsvReader::readUploaded($file)['rows']);
    }

    public function importRows(array $rows): array
    {
        $result = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        if ($rows === []) {
            $result['errors'][] = 'The CSV file has no data rows.';
            return $result;
        }

        foreach ($rows as $row) {
            $line = $row['_line'] ?? '?';
            $name = $this->value($row, ['org_name', 'organization', 'name']);

            if ($name === '') {
                $result['skipped']++;
                $result['errors'][] = "Line {$line}: ORG_NAME is required.";
                continue;
            }

            $payload = [
                'ORG_NAME' => $name,
                'LEADERSHIP_COMPOSITION' => $this->nullable($this->value($row, ['leadership_composition', 'leadership'])),
                'PRIMARY_CONTACT' => $this->nullable($this->value($row, ['primary_contact', 'contact'])),
                'IS_PRIMARY_CONTACT_BAHUJAN' => $this->nullable($this->value($row, ['is_primary_contact_bahujan'])),
                'WEBSITE' => $this->nullable($this->value($row, ['website', 'url'])),
                'INSTAGRAM' => $this->nullable($this->value($row, ['instagram'])),
                'EMAIL' => $this->nullable($this->value($row, ['email'])),
                'PHONE_COUNTRY_CODE' => $this->nullable($this->value($row, ['phone_country_code'])),
                'PHONE_NUMBER' => $this->nullable($this->value($row, ['phone_number', 'phone'])),
                'WHATSAPP_COUNTRY_CODE' => $this->nullable($this->value($row, ['whatsapp_country_code'])),
                'WHATSAPP_NUMBER' => $this->nullable($this->value($row, ['whatsapp_number', 'whatsapp'])),
                'COVER_PIC' => $this->nullable($this->value($row, ['cover_pic', 'cover', 'image'])),
                'DESCRIPTION' => $this->nullable($this->value($row, ['description', 'about'])),
                'ORG_TIME_ZONE' => $this->nullable($this->value($row, ['org_time_zone', 'timezone', 'time_zone'])) ?: 'Asia/Kolkata',
                'LOCATION_NAME' => $this->nullable($this->value($row, ['location_name', 'location'])),
                'COUNTRY' => $this->nullable($this->value($row, ['country'])) ?: 'India',
                'STATE_PROVINCE' => $this->nullable($this->value($row, ['state_province', 'state'])),
                'CITY_TOWN' => $this->nullable($this->value($row, ['city_town', 'city'])),
                'ADDRESS_LINE_1' => $this->nullable($this->value($row, ['address_line_1', 'address'])),
                'ADDRESS_LINE_2' => $this->nullable($this->value($row, ['address_line_2'])),
                'LANDMARK' => $this->nullable($this->value($row, ['landmark'])),
                'GEO_LOCATION' => $this->nullable($this->value($row, ['geo_location'])),
            ];

            $existing = Organization::query()->where('ORG_NAME', $name)->first();
            if ($existing) {
                $existing->fill($payload)->save();
                $result['updated']++;
            } else {
                Organization::create($payload);
                $result['imported']++;
            }
        }

        return $result;
    }

    protected function value(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && trim((string) $row[$key]) !== '') {
                return trim((string) $row[$key]);
            }
        }

        return '';
    }

    protected function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
