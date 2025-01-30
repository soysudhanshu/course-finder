<?php

namespace Tests\Feature;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use App\Enums\RangeEnum;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use DateInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Course[]
     */
    protected array $courses;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->courses = [
            $this->addCourse('Course name', 'Course description'),
            $this->addCourse('Computer Science', 'Computer Science description'),
            $this->addCourse('Accounting', 'In this module, you will learn about accounting'),
        ];

        $this->user = User::factory()->create();
    }

    

    public function testDeletesCourse(): void
    {
        $course = $this->courses[0];

        Sanctum::actingAs($this->user);
        $response = $this->delete('/api/courses/' . $course->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
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

        $this->assertResponseContainsCourse($response, $course);
    }

    public function testDifficultySearch(): void
    {
        $response = $this->requestCourses([
            'difficulty' => [
                CourseDifficultyEnum::ADVANCED->value,
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        /**
         * Attach difficulty and verify results.
         */
        $course = $this->courses[0];
        $course->difficulty = CourseDifficultyEnum::ADVANCED;
        $course->save();

        $response = $this->requestCourses([
            'difficulty' => [
                CourseDifficultyEnum::ADVANCED->value,
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $this->assertResponseContainsCourse($response, $course);
    }

    public function testDifficultyParamAllowMultiple(): void
    {
        $this->setCourseProperties(
            $this->courses[0],
            ['difficulty' => CourseDifficultyEnum::ADVANCED]
        );

        $this->setCourseProperties(
            $this->courses[1],
            ['difficulty' => CourseDifficultyEnum::INTERMEDIATE]
        );

        $response = $this->requestCourses([
            'difficulty' => [
                CourseDifficultyEnum::ADVANCED->value,
                CourseDifficultyEnum::INTERMEDIATE->value,
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $results = array_column($response->json('data'), 'id');

        $this->assertContains($this->courses[0]->id, $results);
        $this->assertContains($this->courses[1]->id, $results);
    }

    public function testDurationParam(): void
    {
        $this->setCourseProperties($this->courses[0], ['duration' => 10]);

        $response = $this->requestCourses([
            'duration' => [
                RangeEnum::BETWEEN->optionValue(2, 10),
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }

    public function testDurationParamAllowsMultiple(): void
    {
        $this->setCourseProperties($this->courses[0], ['duration' => 5]);
        $this->setCourseProperties($this->courses[1], ['duration' => 20]);

        $response = $this->requestCourses([
            'duration' => [
                RangeEnum::BETWEEN->optionValue(2, 7),
                RangeEnum::MORE_THAN->optionValue(10),
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $results = array_column($response->json('data'), 'id');

        $this->assertContains($this->courses[0]->id, $results);
        $this->assertContains($this->courses[1]->id, $results);
    }


    public function testStarRatingParam(): void
    {
        $this->setCourseProperties($this->courses[0], ['rating' => 4]);
        $this->setCourseProperties($this->courses[1], ['rating' => 3]);

        $response = $this->requestCourses([
            'rating' => RangeEnum::MORE_THAN->optionValue(3),
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }


    public function testOnlyCertifiedCourseParam(): void
    {
        $this->setCourseProperties($this->courses[0], ['is_certified' => true]);
        $this->setCourseProperties($this->courses[1], ['is_certified' => false]);

        $response = $this->requestCourses([
            'certified' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }

    public function testReleaseParam(): void
    {
        $this->setCourseProperties(
            $this->courses[0],
            ['created_at' => now()->subDays(27)]
        );

        $this->setCourseProperties(
            $this->courses[1],
            ['created_at' => now()->subMonth(2)]
        );

        $this->setCourseProperties(
            $this->courses[2],
            ['created_at' => now()->subMonth(8)]
        );


        $options = [1, 6, 12];

        foreach ($options as $index => $option) {
            $response = $this->requestCourses([
                'released' => RangeEnum::BETWEEN->optionValue($option, 0),
            ]);

            $response->assertStatus(200);
            $response->assertJsonCount($index + 1, 'data');

            $this->assertResponseContainsCourse($response, $this->courses[$index]);
        }
    }

    public function testFormatParam(): void
    {
        $this->setCourseProperties(
            $this->courses[0],
            ['format' => CourseFormatEnum::VIDEO->value]
        );

        $this->setCourseProperties(
            $this->courses[1],
            ['format' => CourseFormatEnum::INTERACTIVE->value]
        );

        $response = $this->requestCourses([
            'format' => CourseFormatEnum::VIDEO->value,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }


    public function testPriceParam(): void
    {
        $this->setCourseProperties(
            $this->courses[0],
            ['price' => 0]
        );

        $this->setCourseProperties(
            $this->courses[1],
            ['price' => 100]
        );

        $this->setCourseProperties(
            $this->courses[2],
            ['price' => 50]
        );

        $response = $this->requestCourses([
            'price_min' => 0,
            'price_max' => 50,
        ]);

        $response = $this->requestCourses([
            'free_courses_only' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }

    public function testPriceParamRange(): void
    {
        $this->setCourseProperties(
            $this->courses[0],
            ['price' => 0]
        );

        $this->setCourseProperties(
            $this->courses[1],
            ['price' => 50]
        );

        $this->setCourseProperties(
            $this->courses[2],
            ['price' => 100]
        );

        $response = $this->requestCourses([
            'price_min' => 0,
            'price_max' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
        $this->assertResponseContainsCourse($response, $this->courses[1]);
    }


    public function testPopularityParam(): void
    {
        $this->setCourseProperties($this->courses[0], ['popularity' => CoursePopularityEnum::MOST_ENROLLED->value]);
        $this->setCourseProperties($this->courses[1], ['popularity' => CoursePopularityEnum::RECENTLY_VIEWED->value]);
        $this->setCourseProperties($this->courses[2], ['popularity' => CoursePopularityEnum::RECOMMENDED->value]);

        $response = $this->requestCourses([
            'popularity' => CoursePopularityEnum::MOST_ENROLLED->value,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertResponseContainsCourse($response, $this->courses[0]);
    }

    protected function requestCourses(array $params = []): TestResponse
    {
        $response = $this->get('/api/courses?' . http_build_query($params));

        return $response;
    }

    protected function setCourseProperties(Course $course, array $properties): void
    {
        foreach ($properties as $key => $value) {
            $course->$key = $value;
        }

        $course->save();
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

    protected function assertResponseContainsCourse(TestResponse $response, Course $course): void
    {
        $courses = array_column($response->json('data'), 'id');

        $this->assertContains($course->id, $courses);
    }
}
