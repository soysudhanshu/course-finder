<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property CourseCategory[] $categories
 */
class Course extends Model
{
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
