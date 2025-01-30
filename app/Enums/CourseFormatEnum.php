<?php

namespace App\Enums;

enum CourseFormatEnum: string
{
    case VIDEO = 'video';
    case TEXT = 'text';
    case INTERACTIVE = 'interactive';

    public function name(): string
    {
        return match ($this) {
            self::VIDEO => 'Video',
            self::TEXT => 'Text',
            self::INTERACTIVE => 'Interactive/Live',
        };
    }
}
