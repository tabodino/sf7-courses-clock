<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RemainingDurationExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('remaining_duration', [$this, 'formatRemainingDuration']),
        ];
    }

    public function formatRemainingDuration(int $duration): string
    {
        $result = '';
        if (empty($duration) || $duration < 0) {
            return $result;
        }

        $days = round(($duration / (60 * 60 * 24)));

        if ($days >= 1) {
            $result = sprintf("%s days left", $days);
        } else {
            $minutes = floor($duration / 60);
            if ($minutes > 60) {
                $hours = floor($minutes / 60);
                $minutesLeft = $minutes % 60;
                $result = sprintf("%s h %s min left", $hours, sprintf('%02d', $minutesLeft));
            } else {
                $result = sprintf("%s min left", $minutes);
            }
        }
        return $result;
    }
}
