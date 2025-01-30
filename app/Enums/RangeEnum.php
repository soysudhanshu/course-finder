<?php

namespace App\Enums;

enum RangeEnum: string
{
    case BETWEEN = 'BETWEEN';
    case MORE_THAN = 'MORE_THAN';
    case LESS_THAN = 'LESS_THAN';

    public function optionValue(int $start, ?int $end = null): string
    {
        /**
         * Some parameters require end value,
         * so we need to check if it's provided.
         */
        if (in_array($this, [self::BETWEEN]) && is_null($end)) {
            throw new \InvalidArgumentException('End value is required for this range');
        }

        return match ($this) {
            self::BETWEEN => sprintf('%s_%d_TO_%d', $this->value, $start, $end),
            self::MORE_THAN => sprintf('%s_%d', $this->value, $start),
            self::LESS_THAN => sprintf('%s_%d', $this->value, $start),
        };
    }

    public static function parseOption(string $option): ?array
    {
        $type = null;

        foreach (self::cases() as $range) {
            if (str_starts_with($option, $range->value)) {
                $type = $range;
                break;
            }
        }

        if (self::BETWEEN === $type) {
            $values = str_replace(['BETWEEN_', '_TO_'], ' ', $option);
            $values = trim($values);
            $values = explode(' ', $values);

            return [
                'type' => $type,
                'start' => $values[0],
                'end' => $values[1],
            ];
        }

        if (self::MORE_THAN === $type) {
            $value = str_replace('MORE_THAN_', '', $option);

            return [
                'type' => $type,
                'start' => $value,
            ];
        }

        if (self::LESS_THAN === $type) {
            $value = str_replace('LESS_THAN_', '', $option);

            return [
                'type' => $type,
                'start' => $value,
            ];
        }


        return null;
    }
}
