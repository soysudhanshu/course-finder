<?php

namespace App\Http\Requests;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'categories' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'categories.*' => [
                'sometimes',
                'integer',
                'exists:course_categories,id',
            ],
            'difficulty' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'difficulty.*' => [
                'sometimes',
                Rule::enum(CourseDifficultyEnum::class),
            ],
            'duration' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'duration.*' => [
                'sometimes',
                'string',
            ],
            'rating' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'certified' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'released' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'format' => [
                'sometimes',
                'nullable',
                Rule::enum(CourseFormatEnum::class),
            ],
            'free_courses_only' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'price_min' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0'
            ],
            'price_max' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0'
            ],
            'popularity' => [
                'sometimes',
                'nullable',
                Rule::enum(CoursePopularityEnum::class),
            ]
        ];
    }
}
