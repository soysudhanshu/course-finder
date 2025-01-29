<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $file = base_path('database/sample.csv');

        $csv = Reader::createFromPath($file, 'r');

        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            Course::create($record);
        }
    }
}
