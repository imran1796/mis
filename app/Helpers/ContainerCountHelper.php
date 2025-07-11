<?php

namespace App\Helpers;

class ContainerCountHelper
{
    public static function calculateTeu($record): array
    {
        $laden = (int) ($record->dc20 ?? 0)
            + (int) ($record->r20 ?? 0)
            + 2 * (
                (int) ($record->dc40 ?? 0)
                + (int) ($record->dc45 ?? 0)
                + (int) ($record->r40 ?? 0)
            );

        $empty = (int) ($record->mty20 ?? 0)
            + 2 * (int) ($record->mty40 ?? 0);

        return ['laden' => $laden, 'empty' => $empty, 'total' => $laden + $empty];
    }

    public static function calculateBox($record): array
    {
        $laden = (int) ($record->dc20 ?? 0)
            + (int) ($record->r20 ?? 0)
            + (int) ($record->dc40 ?? 0)
            + (int) ($record->dc45 ?? 0)
            + (int) ($record->r40 ?? 0);

        $empty = (int) ($record->mty20 ?? 0)
            + (int) ($record->mty40 ?? 0);

        return ['laden' => $laden, 'empty' => $empty, 'total' => $laden + $empty];
    }

    public static function zeroIfEmpty($value)
    {
        return $value ?: 0;
    }

    public static function charZeroIfEmpty($value)
    {
        return $value ?: '0';
    }

    public static function percent($part, $whole): float
    {
        $whole = $whole ?: 1;
        return round(($part / $whole) * 100, 2);
    }
}
