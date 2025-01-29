<?php

namespace App\Http\Controllers;

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

        return new CourseCollection($query->paginate());
    }
}
