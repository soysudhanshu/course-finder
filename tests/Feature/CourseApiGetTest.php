<?php

namespace Tests\Feature;

use App\Enums\CourseFormatEnum;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseApiGetTest extends TestCase
{
    use RefreshDatabase;

    public function testAddCourse(): void
    {
        $input = [
            'name' => 'Course name',
            'description' => 'Course description',
            'difficulty' => 1,
            'format' => 'video',
            'duration' => '2',
            'categories' => [$this->createCourseCategory()->id],
            'rating' => 4.5,
            'is_certified' => true,
            'format' => CourseFormatEnum::INTERACTIVE->value,
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

    protected function createCourseCategory(): CourseCategory
    {
        $category = new CourseCategory();

        $category->name = "Category 1";

        $category->save();

        return $category;
    }
}
