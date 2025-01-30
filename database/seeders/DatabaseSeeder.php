<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = $this->createCategories();

        Course::factory(10)->create(['price' => 0]);

        $courses = Course::factory()->createMany(10000);



        foreach ($courses as $course) {
            $course->categories()->sync($categories->random());
            $course->save();
        }
    }

    protected function createCategories(): Collection
    {
        $categories = collect([
            'Mathematics',
            'Biology',
            'History',
            'Geography',
            'Philosophy',
            'Economics',
            'Political Science',
            'Sociology',
            'Psychology',
            'Art',
            'Music',
            'Drama',
            'Physical Education',
            'Engineering',
            'Medicine',
        ]);

        return $categories->map(fn($category) => CourseCategory::create(['name' => $category]));
    }
}
