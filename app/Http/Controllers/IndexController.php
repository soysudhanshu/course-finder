<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public  function __invoke()
    {
        $categories = CourseCategory::all()->map(
            fn(CourseCategory $cat) => ['label' => $cat->name, 'value' => $cat->id]
        );

        return view('index', [
            'categories' => $categories,
        ]);
    }
}
