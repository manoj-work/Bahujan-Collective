<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PlaceSearchService
{
    /**
     * @return array<int, array{label: string, value: string, city: ?string, state: ?string, country: ?string}>
     */
    public function search(string $query): array
    {
        $query = Str::limit(trim($query), 80, '');
        if (mb_strlen($query) < 2) {
            return [];
        }

        $cacheKey = 'places:photon:'.md5(mb_strtolower($query));

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query) {
            return $this->searchPhoton($query);
        });
    }

    /**
     * @return array<int, array{label: string, value: string, city: ?string, state: ?string, country: ?string}>
     */
    protected function searchPhoton(string $query): array
    {
        $response = Http::timeout(5)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => config('app.name', 'Bahujan Collective').' place search',
            ])
            ->get('https://photon.komoot.io/api/', [
                'q' => $query,
                'limit' => 6,
                'lang' => 'en',
                'lat' => 22.9734,
                'lon' => 78.6569,
            ]);

        if (! $response->successful()) {
            return [];
        }

        $features = $response->json('features') ?? [];
        $places = [];
        $seen = [];

        foreach ($features as $feature) {
            $properties = $feature['properties'] ?? [];
            $name = trim((string) ($properties['name'] ?? ''));
            $city = trim((string) ($properties['city'] ?? $properties['locality'] ?? ''));
            $state = trim((string) ($properties['state'] ?? $properties['county'] ?? ''));
            $country = trim((string) ($properties['country'] ?? ''));
            $value = $city !== '' ? $city : $name;

            if ($value === '') {
                continue;
            }

            $labelParts = array_values(array_unique(array_filter([$name !== $value ? $name : $value, $state, $country])));
            $label = implode(', ', $labelParts);
            $fingerprint = mb_strtolower($label);

            if (isset($seen[$fingerprint])) {
                continue;
            }

            $seen[$fingerprint] = true;
            $places[] = [
                'label' => $label,
                'value' => $value,
                'city' => $city !== '' ? $city : null,
                'state' => $state !== '' ? $state : null,
                'country' => $country !== '' ? $country : null,
                'in_india' => strcasecmp($country, 'India') === 0 || strtoupper((string) ($properties['countrycode'] ?? '')) === 'IN',
            ];
        }

        usort($places, fn ($a, $b) => ($a['in_india'] === $b['in_india'] ? 0 : ($a['in_india'] ? -1 : 1)));

        return array_map(function (array $place) {
            unset($place['in_india']);
            return $place;
        }, array_values($places));
    }
}
