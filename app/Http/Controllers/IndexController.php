<?php

namespace App\Http\Controllers;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use App\Enums\CoursePopularityEnum;
use App\Enums\RangeEnum;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IndexController extends Controller
{
    public  function __invoke()
    {

        return view('index', [
            'filters' => $this->getFilters(),
        ]);
    }

    private function getFilters(): array
    {
        return [
            [
                'type' => 'toggle',
                'label' => 'Show only certification courses',
                'name' => 'certified',
                'options' => $this->getCourseFormatOptions(),
            ],
            [
                'type' => 'search',
                'name' => 'search',
                'label' => 'Search',
                'placeholder' => 'Search by course name',
            ],
            [
                'type' => 'range',
                'max' => $this->getCourseMaxPrice(),
            ],
            [
                'type' => 'checklist',
                'name' => 'categories',
                'label' => 'Categories',
                'options' => $this->getCourseCategoryOptions(),
            ],
            [
                'type' => 'checklist',
                'name' => 'difficulty',
                'label' => 'Difficulty',
                'options' => $this->getDifficultyLevelOptions(),
            ],
            [
                'type' => 'checklist',
                'name' => 'duration',
                'label' => 'Duration',
                'options' => $this->getDurationOptions(),
            ],
            [
                'type' => 'radio',
                'name' => 'rating',
                'label' => 'Star Rating',
                'options' => $this->getStarRatingOptions(),
            ],
            [
                'type' => 'radio',
                'name' => 'release_date',
                'label' => 'Release Date',
                'options' => $this->getReleaseDateOptions(),
            ],
            [
                'type' => 'radio',
                'name' => 'format',
                'label' => 'Course Format',
                'options' => $this->getCourseFormatOptions(),
            ],
            [
                'type' => 'radio',
                'name' => 'popularity',
                'label' => 'Popularity',
                'options' => $this->getPopularityOptions(),
            ]

        ];
    }


    protected function getCourseMaxPrice(): int
    {
        return ceil(Course::max('price'));
    }
    protected function getCourseCategoryOptions(): Collection
    {
        return CourseCategory::all()->map(function (CourseCategory $category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
    }


    private function getDifficultyLevelOptions(): Collection
    {
        return $difficultyLevels = Course::select('difficulty')
            ->distinct()
            ->pluck('difficulty')
            ->map(function (CourseDifficultyEnum $difficulty) {
                return [
                    'label' => $difficulty->name(),
                    'value' => $difficulty->value,
                ];
            });
    }

    private function getDurationOptions(): array
    {
        return [
            [
                'label' => 'Less than 2 hours',
                'value' => RangeEnum::BETWEEN->optionValue(0, 2),
            ],
            [
                'label' => '2-5 hours',
                'value' => RangeEnum::BETWEEN->optionValue(2, 5),
            ],
            [
                'label' => '5-10 hours',
                'value' => RangeEnum::BETWEEN->optionValue(5, 10),
            ],
            [
                'label' => 'More than 10 hours',
                'value' => RangeEnum::MORE_THAN->optionValue(10),
            ],
        ];
    }

    private function getStarRatingOptions(): array
    {
        return [
            [
                'label' => '4+ stars',
                'value' => RangeEnum::MORE_THAN->optionValue(4),
            ],
            [
                'label' => '3+ stars',
                'value' => RangeEnum::MORE_THAN->optionValue(2),
            ],
            [
                'label' => '2 stars and below',
                'value' => RangeEnum::BETWEEN->optionValue(0, 2),
            ],
        ];
    }

    private function getReleaseDateOptions(): array
    {
        return [
            [
                'label' => 'Last 30 days',
                'value' => RangeEnum::BETWEEN->optionValue(1, 0),
            ],
            [
                'label' => 'Last 6 months',
                'value' => RangeEnum::BETWEEN->optionValue(6, 0),
            ],
            [
                'label' => 'Last 1 year',
                'value' => RangeEnum::BETWEEN->optionValue(12, 0),
            ]
        ];
    }

    private function getCourseFormatOptions(): array
    {
        return array_map(function (CourseFormatEnum $format) {
            return [
                'label' => $format->name(),
                'value' => $format->value,
            ];
        }, CourseFormatEnum::cases());
    }

    private function getPopularityOptions(): array
    {
        return array_map(function (CoursePopularityEnum $enum) {
            return [
                'label' => $enum->label(),
                'value' => $enum->value,
            ];
        }, CoursePopularityEnum::cases());
    }
}
