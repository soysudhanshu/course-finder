<?php

namespace Tests\Feature;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CourseApiGetTest extends TestCase
{
    use RefreshDatabase;

    protected CourseCategory $category;


    public function setUp(): void
    {
        parent::setUp();

        $this->category = $this->createCourseCategory('Category 1');
    }

    public function testAddCourse(): void
    {
        $input = [
            'name' => 'Course name',
            'description' => 'Course description',
            'difficulty' => 1,
            'format' => 'video',
            'duration' => '2',
            'categories' => [$this->category->id],
            'rating' => 4.5,
            'is_certified' => true,
            'format' => CourseFormatEnum::INTERACTIVE->value,
            'price' => 100,
        ];

        $response = $this->post('/api/courses', $input);

        $response->assertCreated();

        $this->assertDatabaseCount('courses', 1);

        $databaseEntry = $input;

        unset($databaseEntry['categories']);

        $this->assertDatabaseHas('courses', $databaseEntry);

        Course::first()->categories()->get()->each(function (CourseCategory $category) use ($input) {
            $this->assertContains($category->id, $input['categories']);
        });
    }

    public function testPutRequest(): void
    {
        $newCategory = $this->createCourseCategory('New Category');

        $course = Course::factory()->createOne();

        $input = $course->toArray();

        $input['name'] = 'New name';
        $input['description'] = 'New description';
        $input['difficulty'] = CourseDifficultyEnum::ADVANCED->value;
        $input['format'] = CourseFormatEnum::INTERACTIVE->value;
        $input['duration'] = '3';
        $input['categories'] = [$newCategory->id];
        $input['rating'] = 4.5;
        $input['is_certified'] = false;
        $input['price'] = 200;

        $response = $this->sendPutRequest($course->id, $input);
        $response->assertOk();

        $validatedInput = $input;
        unset($validatedInput['categories'], $validatedInput['created_at'], $validatedInput['updated_at'], $validatedInput['difficulty']);

        $course->refresh();

        $course->categories()->get()->each(function (CourseCategory $category) use ($newCategory) {
            $this->assertEquals($newCategory->id, $category->id);
        });
    }


    protected function getUserInput(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Course name',
            'description' => 'Course description',
            'format' => CourseFormatEnum::INTERACTIVE->value,
            'difficulty' => CourseDifficultyEnum::BEGINNER->value,
            'duration' => '2',
            'categories' => [$this->category->id],
            'rating' => 4.5,
            'is_certified' => true,
        ], $overrides);
    }

    protected function sendPutRequest(int $id, array $input): TestResponse
    {
        return  $this->put("/api/courses/{$id}", $input);
    }

    protected function createCourseCategory(string $name): CourseCategory
    {
        $category = new CourseCategory();

        $category->name = "Category 1";

        $category->save();

        return $category;
    }
}
