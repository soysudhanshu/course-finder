<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Course[]
     */
    protected array $courses;

    public function setUp(): void
    {
        parent::setUp();

        $this->courses = [
            $this->addCourse('Course name', 'Course description'),
            $this->addCourse('Computer Science', 'Computer Science description'),
            $this->addCourse('Accounting', 'In this module, you will learn about accounting'),
        ];
    }

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
        $course = $this->courses[0];


        $response = $this->requestCourses(['search' => $course->name]);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');


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
        $this->addCourse('Film Making', 'In this module, you will learn about cinematic film making');

        $response = $this->requestCourses(['search' => 'cinematic']);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function testSearchParamTokenisesInput(): void
    {
        $search = implode(' ', array_column($this->courses, 'name'));

        $response = $this->requestCourses(['search' => $search]);
        $response->assertStatus(200);
        $response->assertJsonCount(count($this->courses), 'data');
    }

    public function testCategorySearch(): void
    {
        $category = $this->addCourseCategory('Programming');

        $course = $this->courses[0];

        $course->categories()->attach($category);

        $response = $this->requestCourses(['categories' => [$category->id]]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            "data" => [
                [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                ]
            ]
        ]);
    }

    protected function requestCourses(array $params = []): TestResponse
    {
        $response = $this->get('/api/courses?' . http_build_query($params));

        return $response;
    }

    protected function addCourse(string $name, string $description): Course
    {
        $course = new Course();

        $course->name = $name;
        $course->description = $description;
        $course->save();

        return $course;
    }

    protected function addCourseCategory(string $name): CourseCategory
    {
        $category = new CourseCategory();

        $category->name = $name;
        $category->save();

        return $category;
    }
}
