<?php

namespace App\Models;

use App\Enums\CourseDifficultyEnum;
use App\Enums\CourseFormatEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property CourseCategory[] $categories
 * @property CourseDifficultyEnum $difficulty
 * @property int $duration
 * @property float $rating
 * @property bool $is_certified
 * @property mixed $name
 * @property float $price
 */
class Course extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'difficulty' => CourseDifficultyEnum::class,
            'is_certified' => 'boolean',
            'duration' => 'integer',
            'rating' => 'float',
            'format' =>  CourseFormatEnum::class,
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseCategory::class,
            'course_category_relation',
            'course_id',
            'course_category_id',
            'id',
            'id'
        );
    }
}
