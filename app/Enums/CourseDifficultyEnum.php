<?php

namespace App\Enums;

enum CourseDifficultyEnum: int
{
    case BEGINNER = 1;
    case INTERMEDIATE = 2;
    case ADVANCED = 3;

    public function name(): string
    {
        return match ($this) {
            self::BEGINNER => 'Beginner',
            self::INTERMEDIATE => 'Intermediate',
            self::ADVANCED => 'Advanced',
            default => 'Unknown',
        };
    }
}
