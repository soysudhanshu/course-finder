<?php

namespace App\Http\Controllers;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\RangeEnum;
use App\Http\Requests\AddCourseRequest;
use App\Http\Resources\Course as ResourcesCourse;
use App\Http\Resources\CourseCollection;
use App\Models\Course;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('search')) {
            $query->where(function (Builder $query) use ($request) {
                $search = $request->search;
                $search = trim($search);
                $search = explode(' ', $search);

                foreach ($search as $term) {
                    $query->where('name', 'like', "%$term%")
                        ->orWhere('description', 'like', "%$term%");
                }

                return $query;
            });
        }


        if ($request->has('categories')) {
            $query->whereHas('categories', function (Builder $query) use ($request) {
                $query->whereIn('id', $request->categories);
            });
        }

        if ($request->has('difficulty')) {
            $query->whereIn('difficulty', $request->difficulty);
        }


        if ($request->has('duration')) {

            $query->where(function (Builder $query) use ($request) {
                foreach ($request->duration as $duration) {
                    $duration = RangeEnum::parseOption($duration);

                    if (is_null($duration)) {
                        continue;
                    }

                    if ($duration['type'] === RangeEnum::BETWEEN) {
                        $query->whereBetween(
                            'duration',
                            [$duration['start'], $duration['end']],
                            'or'
                        );
                    }

                    if ($duration['type'] === RangeEnum::MORE_THAN) {
                        $query->where(
                            'duration',
                            '>',
                            $duration['start'],
                            'or'
                        );
                    }
                }

                return $query;
            });
        }


        if ($request->has('rating')) {
            $rating = RangeEnum::parseOption($request->rating);

            if (!is_null($rating) && $rating['type'] === RangeEnum::MORE_THAN) {
                $query->where('rating', '>', $rating['start']);
            } elseif (!is_null($rating) && $rating['type'] === RangeEnum::BETWEEN) {
                $query->whereBetween('rating', [$rating['start'], $rating['end']]);
            }
        }

        if ($request->has('certified')) {
            $query->where('is_certified', 1);
        }

        if ($request->has('released')) {
            $release = RangeEnum::parseOption($request->released);

            match ($release['type']) {
                RangeEnum::BETWEEN => $query->whereBetween(
                    'created_at',
                    [now()->subMonths($release['start']), now()],
                    'or'
                ),
            };
        }

        if ($request->has('format')) {
            $query->where('format', $request->format);
        }

        if ($request->has('free_courses_only')) {
            $query->where('price', 0);
        } elseif ($request->has('price_min') && $request->has('price_max')) {
            $query->whereBetween('price', [$request->price_min, $request->price_max]);
        }

        if ($request->has('popularity')) {
            $query->where('popularity', $request->popularity);
        }

        return new CourseCollection($query->paginate(
            page: $request->page ?? 1,
        ));
    }

    public function show($id)
    {
        return new ResourcesCourse(Course::findOrFail($id));
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidations());

        if (!$validator->passes()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $inputs = $validator->validated();

        $course  = new Course();

        $course->name = $inputs['name'];
        $course->description = $inputs['description'];
        $course->difficulty = $inputs['difficulty'];
        $course->duration = $inputs['duration'];
        $course->rating = $inputs['rating'];
        $course->is_certified = $inputs['is_certified'];
        $course->format = $inputs['format'];

        $course->save();


        return response()->json(
            ResourcesCourse::make($course),
            Response::HTTP_CREATED
        );
    }

    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['Course not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), $this->getValidations());

        if (!$validator->passes()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $inputs = $validator->validated();

        $course->name = $inputs['name'];
        $course->description = $inputs['description'];
        $course->difficulty = $inputs['difficulty'];
        $course->duration = $inputs['duration'];
        $course->rating = $inputs['rating'];
        $course->is_certified = $inputs['is_certified'];
        $course->format = $inputs['format'];

        $course->save();

        $course->categories()->sync($inputs['categories']);

        return response()->json(
            ResourcesCourse::make($course),
            Response::HTTP_OK
        );
    }

    public function delete($id)
    {
        $course = Course::find($id);

        if ($course) {
            $course->delete();
        }

        return response()->json(['Course successfully deleted'], Response::HTTP_OK);
    }

    public function getValidations(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'max:500',
            ],
            'difficulty' => [
                'required',
                Rule::enum(CourseDifficultyEnum::class),
            ],
            'duration' => [
                'required',
                'string',
                'max:255',
            ],
            'rating' => [
                'required',
                'numeric',
                'min:0',
                'max:5',
            ],
            'is_certified' => [
                'required',
                'boolean',
            ],
            'format' => [
                'required',
                Rule::enum(CourseFormatEnum::class),
            ],
            'categories' => [
                'required',
                'array',
                Rule::exists('course_categories', 'id'),
            ],
        ];
    }
}
