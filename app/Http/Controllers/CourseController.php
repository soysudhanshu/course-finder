<?php

namespace App\Http\Controllers;

use App\Enums\RangeEnum;
use App\Http\Resources\CourseCollection;
use App\Models\Course;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return new CourseCollection($query->paginate(
            page: $request->page ?? 1,
        ));
    }
}
