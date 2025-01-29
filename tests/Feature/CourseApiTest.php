<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testCourseRetrieval(): void
    {
        $response = $this->get('/api/courses');

        $response->assertStatus(200);
    }

    public function testCourseSearchParam(): void
    {
        $this->addCourse('Course name', 'Course description');
        $this->addCourse('Computer Science', 'Computer Science description');


        $response = $this->get('/api/courses?search=hello');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEmpty($data, 'Search should return empty result');

        $response = $this->get('/api/courses?search=Course');
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotEmpty($data, 'Search should return result');
        $this->assertCount(1, $data, 'Search should return one result');
        $response->assertJson([
            'data' => [
                [
                    'name' => 'Course name',
                    'description' => 'Course description',
                ],
            ],
        ]);
    }


    public function testSearchParamInDescription(): void
    {
        $this->addCourse('Accounting', 'In this module, you will learn about accounting');

        $this->addCourse('Course name', 'Course description in the middle');
        $this->addCourse('Computer Science', 'Computer Science description');

        $response = $this->get('/api/courses?search=description');
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotEmpty($data, 'Search should return result');
        $this->assertCount(2, $data, 'Search should return one result');
        $response->assertJson([
            'data' => [
                [
                    'name' => 'Course name',
                    'description' => 'Course description in the middle',
                ],
                [
                    'name' => 'Computer Science',
                    'description' => 'Computer Science description',
                ]
            ],
        ]);
    }

    public function testSearchParamUsesWords(): void
    {
        $this->addCourse('Film Making', 'In this module, you will learn about film making');

        $this->addCourse('Accounting', 'In this module, you will learn about accounting');
        $this->addCourse('Computer Science', 'Computer Science description');

        $response = $this->get('/api/courses?search=accounting science');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data, 'Search should return result');
        $this->assertCount(2, $data, 'Search should return one result');
        $response->assertJson([
            'data' => [
                [
                    'name' => 'Accounting',
                    'description' => 'In this module, you will learn about accounting',
                ],
                [
                    'name' => 'Computer Science',
                    'description' => 'Computer Science description',
                ]
            ],
        ]);
    }

    protected function addCourse(string $name, string $description): Course
    {
        $course = new Course();

        $course->name = $name;
        $course->description = $description;
        $course->save();

        return $course;
    }
}
