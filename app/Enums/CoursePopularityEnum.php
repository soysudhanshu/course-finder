<?php

namespace App\Enums;

enum CoursePopularityEnum: string
{
    case MOST_ENROLLED = 'most_enroller';
    case RECOMMENDED = 'recommended';
    case RECENTLY_VIEWED = 'recently_viewed';
    case TRENDING = 'trending';

    public function label(): string
    {
        return match ($this) {
            self::MOST_ENROLLED => 'Most Enrolled',
            self::RECOMMENDED => 'Recommended',
            self::RECENTLY_VIEWED => 'Recently Viewed',
            self::TRENDING => 'Trending',
        };
    }
}
