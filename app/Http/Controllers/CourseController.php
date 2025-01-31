<?php

namespace App\Http\Controllers;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use App\Enums\RangeEnum;
use App\Http\Requests\AddCourseRequest;
use App\Http\Requests\CourseSearchRequest;
use App\Http\Requests\CourseUpdateRequest;
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

    public function index(CourseSearchRequest $request)
    {
        $validated = $request->validated();

        $query = Course::query();

        $search = $validated['search'] ?? '';

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $search = trim($search);
                $search = explode(' ', $search);

                foreach ($search as $term) {
                    $query->where('name', 'like', "%$term%")
                        ->orWhere('description', 'like', "%$term%");
                }

                return $query;
            });
        }

        $categories = $validated['categories'] ?? [];

        if ($categories) {
            $query->whereHas('categories', function (Builder $query) use ($categories) {
                $query->whereIn('id', $categories);
            });
        }

        $difficulty = $validated['difficulty'] ?? [];

        if ($difficulty) {
            $query->whereIn('difficulty', $difficulty);
        }


        $durations = $validated['duration'] ?? [];

        if ($durations) {

            $query->where(function (Builder $query) use ($durations) {
                foreach ($durations as $duration) {
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


        $rating = $validated['rating'] ?? '';

        if ($rating) {
            $rating = RangeEnum::parseOption($rating);

            if (!is_null($rating) && $rating['type'] === RangeEnum::MORE_THAN) {
                $query->where('rating', '>', $rating['start']);
            } elseif (!is_null($rating) && $rating['type'] === RangeEnum::BETWEEN) {
                $query->whereBetween('rating', [$rating['start'], $rating['end']]);
            }
        }

        $certified = $validated['certified'] ?? false;
        if ($certified) {
            $query->where('is_certified', 1);
        }

        $release = $validated['released'] ?? '';

        if ($release) {
            $release = RangeEnum::parseOption($release);

            if (is_null($release)) {
                return response()->json(['Invalid release date range'], Response::HTTP_BAD_REQUEST);
            }

            match ($release['type']) {
                RangeEnum::BETWEEN => $query->whereBetween(
                    'created_at',
                    [now()->subMonths($release['start']), now()],
                    'or'
                ),
            };
        }

        $format = $validated['format'] ?? '';

        if ($format) {
            $query->where('format', $format);
        }

        $onlyFree = $request['free_courses_only'] ?? false;
        $minPrice = $request['price_min'] ?? 0;
        $maxPrice = $request['price_max'] ?? 0;

        if ($onlyFree) {
            $query->where('price', 0);
        } elseif (!$onlyFree && ($minPrice || $maxPrice)) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        $popularity = $validated['popularity'] ?? '';

        if ($popularity) {
            $query->where('popularity', $popularity);
        }

        return new CourseCollection($query->paginate(
            page: $request->page ?? 1,
        ));
    }

    public function show($id)
    {
        return new ResourcesCourse(Course::findOrFail($id));
    }

    public function add(CourseUpdateRequest $request)
    {
        $inputs = $request->validated();

        $course  = new Course();

        $course->name = $inputs['name'];
        $course->description = $inputs['description'];
        $course->difficulty = $inputs['difficulty'];
        $course->duration = $inputs['duration'];
        $course->rating = $inputs['rating'];
        $course->is_certified = $inputs['is_certified'];
        $course->format = $inputs['format'];
        $course->price = $inputs['price'];
        $course->popularity = $inputs['popularity'];
        $course->instructor = $inputs['instructor'];


        $course->save();

        return response()->json(
            ResourcesCourse::make($course),
            Response::HTTP_CREATED
        );
    }

    public function update(CourseUpdateRequest $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['Course not found'], Response::HTTP_NOT_FOUND);
        }

        $inputs = $request->validated();

        $course->name = $inputs['name'];
        $course->description = $inputs['description'];
        $course->difficulty = $inputs['difficulty'];
        $course->duration = $inputs['duration'];
        $course->rating = $inputs['rating'];
        $course->is_certified = $inputs['is_certified'];
        $course->format = $inputs['format'];
        $course->price = $inputs['price'];
        $course->popularity = $inputs['popularity'];

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
}
