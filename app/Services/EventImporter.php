<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use App\Support\CsvReader;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class EventImporter
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

        $categoryMap = $this->lookupMap(EventCategory::query()->get(), 'EVENT_CATEGORY_VALUE', 'EVENT_CATEGORY_ID');
        $typeMap = $this->lookupMap(EventType::query()->get(), 'EVENT_TYPE_VALUE', 'EVENT_TYPE_ID');

        foreach ($rows as $row) {
            $line = $row['_line'] ?? '?';
            $name = $this->value($row, ['event_name', 'name', 'title']);

            if ($name === '') {
                $result['skipped']++;
                $result['errors'][] = "Line {$line}: EVENT_NAME is required.";
                continue;
            }

            try {
                $startDate = $this->parseDate($this->value($row, ['start_date', 'date']));
                $endDate = $this->parseDate($this->value($row, ['end_date'])) ?: $startDate;
                $startTime = $this->parseTime($this->value($row, ['start_time', 'time']));
                $endTime = $this->parseTime($this->value($row, ['end_time']));
                $payload = [
                    'EVENT_NAME' => $name,
                    'EVENT_LINK' => $this->nullable($this->value($row, ['event_link', 'link', 'url'])),
                    'COVER_PIC' => $this->nullable($this->value($row, ['cover_pic', 'cover', 'image'])),
                    'TAGLINE' => $this->nullable($this->value($row, ['tagline'])),
                    'DESCRIPTION' => $this->nullable($this->value($row, ['description', 'about'])),
                    'EVENT_CATEGORY_ID' => $this->lookupId($this->value($row, ['event_category', 'category', 'event_category_id']), $categoryMap),
                    'CURRENCY' => $this->nullable($this->value($row, ['currency'])) ?: 'INR',
                    'COST_DONATION' => $this->nullable($this->value($row, ['cost_donation', 'cost', 'price'])),
                    'START_DATE' => $startDate,
                    'START_TIME' => $startTime,
                    'START_AM_PM' => $startTime ? Carbon::createFromFormat('H:i:s', $startTime)->format('A') : null,
                    'END_DATE' => $endDate,
                    'END_TIME' => $endTime,
                    'END_AM_PM' => $endTime ? Carbon::createFromFormat('H:i:s', $endTime)->format('A') : null,
                    'TIME_ZONE' => $this->nullable($this->value($row, ['time_zone', 'timezone'])) ?: 'Asia/Kolkata',
                    'EVENT_TYPE_ID' => $this->lookupId($this->value($row, ['event_type', 'type', 'mode', 'event_type_id']), $typeMap),
                    'LOCATION_NAME' => $this->nullable($this->value($row, ['location_name', 'location', 'venue'])),
                    'COUNTRY' => $this->nullable($this->value($row, ['country'])) ?: 'India',
                    'STATE_PROVINCE' => $this->nullable($this->value($row, ['state_province', 'state'])),
                    'CITY_TOWN' => $this->nullable($this->value($row, ['city_town', 'city'])),
                    'ADDRESS_LINE_1' => $this->nullable($this->value($row, ['address_line_1', 'address'])),
                    'ADDRESS_LINE_2' => $this->nullable($this->value($row, ['address_line_2'])),
                    'LANDMARK' => $this->nullable($this->value($row, ['landmark'])),
                    'EVENT_VISIBILITY' => $this->nullable($this->value($row, ['event_visibility', 'visibility'])) ?: 'Public',
                    'EVENT_RSVP' => $this->nullable($this->value($row, ['event_rsvp', 'rsvp'])) ?: 'Open',
                    'RSVP_VISIBILITY' => $this->nullable($this->value($row, ['rsvp_visibility'])) ?: 'ATTENDEE_CHOICE',
                    'RSVP_COUNT' => (int) ($this->value($row, ['rsvp_count']) ?: 0),
                    'APPROVAL_STATUS' => $this->approvalStatus($this->value($row, ['approval_status', 'status'])),
                    'updated_at' => now(),
                ];

                $existing = Event::query()
                    ->where('EVENT_NAME', $name)
                    ->when(
                        $startDate,
                        fn ($q) => $q->whereDate('START_DATE', $startDate),
                        fn ($q) => $q->whereNull('START_DATE')
                    )
                    ->when(
                        $startTime,
                        fn ($q) => $q->whereTime('START_TIME', $startTime),
                        fn ($q) => $q->whereNull('START_TIME')
                    )
                    ->first();

                if ($existing) {
                    $existing->fill($payload)->save();
                    $result['updated']++;
                } else {
                    $payload['created_at'] = now();
                    Event::create($payload);
                    $result['imported']++;
                }
            } catch (\Throwable $e) {
                $result['skipped']++;
                $result['errors'][] = "Line {$line}: {$e->getMessage()}";
            }
        }

        return $result;
    }

    protected function lookupMap($records, string $valueColumn, string $idColumn): array
    {
        $map = [];
        foreach ($records as $record) {
            $id = $record->{$idColumn};
            $value = (string) $record->{$valueColumn};
            $map[$this->normalizeKey($value)] = $id;
            $map[(string) $id] = $id;
        }

        return $map;
    }

    protected function lookupId(string $value, array $map): ?int
    {
        if ($value === '') {
            return null;
        }

        return $map[$this->normalizeKey($value)] ?? $map[$value] ?? null;
    }

    protected function normalizeKey(string $value): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim($value)));
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

    protected function parseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    protected function parseTime(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            $value .= ':00';
        }

        return Carbon::parse($value)->format('H:i:s');
    }

    protected function approvalStatus(string $value): string
    {
        $status = strtolower($value);
        if (in_array($status, ['approved', 'pending', 'rejected'], true)) {
            return $status;
        }

        return 'approved';
    }
}
