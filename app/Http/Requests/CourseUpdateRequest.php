<?php

namespace App\Http\Requests;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends FormRequest
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
                'numeric',
                'min:0',
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
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'popularity' => [
                'required',
                Rule::enum(CoursePopularityEnum::class),
            ],
            'instructor' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
