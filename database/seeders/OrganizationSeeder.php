<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed the organizations table with sample data for "Featured allies".
     */
    public function run(): void
    {
        $now = now();

        $organizations = [
            [
                'ORG_NAME' => 'Bahujan Shiksha Andolan',
                'WEBSITE' => 'https://bahujanshikshaandolan.org',
                'DESCRIPTION' => 'Education and awareness for Bahujan communities.',
                'COUNTRY' => 'India',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ORG_NAME' => 'Dalit Camera',
                'WEBSITE' => 'https://dalitcamera.com',
                'DESCRIPTION' => 'Media collective amplifying Bahujan voices.',
                'COUNTRY' => 'India',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ORG_NAME' => 'Round Table India',
                'WEBSITE' => 'https://roundtableindia.co.in',
                'DESCRIPTION' => 'Writings and discussions on caste, democracy and justice.',
                'COUNTRY' => 'India',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ORG_NAME' => 'Velivada',
                'WEBSITE' => 'https://velivada.com',
                'DESCRIPTION' => 'Platform for oppressed voices and critical perspectives.',
                'COUNTRY' => 'India',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ORG_NAME' => 'Ambedkar Reading Group',
                'WEBSITE' => null,
                'DESCRIPTION' => 'Local reading circles on Ambedkarite thought.',
                'COUNTRY' => 'India',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($organizations as $org) {
            DB::table('organizations')->updateOrInsert(
                ['ORG_NAME' => $org['ORG_NAME']],
                $org
            );
        }
    }
}
