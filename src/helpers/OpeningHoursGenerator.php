<?php
namespace mfarajewicz\simpleopeninghours\helpers;


class OpeningHoursGenerator
{

    /**
     * Generates lists of hours, plus default null value marked as "--"
     * @return array
     */
    public static function getHours(): array
    {
        $hoursRange = [
            "" => '--'
        ];
        for ($i = 0; $i < 25; $i++) {
            $hoursRange[] = $i . ':00';
        }

        return $hoursRange;
    }

    /**
     * Returns day of week with full name
     *
     * @return array
     */
    public static function getDays(): array
    {
        $daysOfWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $daysOfWeek[] = [
                'name' => jddayofweek($i, 1)
            ];
        }

        return $daysOfWeek;
    }
}