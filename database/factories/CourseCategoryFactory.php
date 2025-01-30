<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseCategory>
 */
class CourseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement($this->subjects()),
        ];
    }

    protected function subjects(): array
    {
        return [
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
        ];
    }
}
