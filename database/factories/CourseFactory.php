<?php

namespace Database\Factories;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use App\Http\Resources\Course;
use BackedEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Validation\Rules\Enum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(mt_rand(5, 10), asText: true),
            'description' => $this->faker->paragraph(),
            'difficulty' => $this->getRandomValue(CourseDifficultyEnum::class),
            'format' => $this->getRandomValue(CourseFormatEnum::class),
            'duration' => $this->faker->numberBetween(1, 200),
            'rating' => $this->faker->randomFloat(1, 0, 5),
            'is_certified' => $this->faker->boolean(),
            'price' => $this->faker->randomFloat(2, 500, 2000),
            'popularity' => $this->getRandomValue(CoursePopularityEnum::class),
            'instructor' => $this->faker->name(),
        ];
    }

    protected function getRandomValue(string $enumClass): mixed
    {
        $cases = $enumClass::cases();
        shuffle($cases);

        return $cases[0]->value;
    }
}
