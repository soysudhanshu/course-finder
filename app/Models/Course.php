<?php

namespace App\Models;

use App\Enums\CourseDifficultyEnum;
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
 *
 */
class Course extends Model
{
    protected function casts(): array
    {
        return [
            'difficulty' => CourseDifficultyEnum::class,
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
