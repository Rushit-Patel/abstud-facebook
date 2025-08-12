<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Carbon\Carbon;


class Helpers
{
    public static function parseToYmd($dateString)
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y', 'm-d-Y'];
        $dateString = trim($dateString);
        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $dateString);
                if ($dt && $dt->format($format) === $dateString) {
                    return $dt->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return null;
    }
    public static function parseToDmy($dateString)
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y', 'm-d-Y'];
        $dateString = trim($dateString);
        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $dateString);
                if ($dt && $dt->format($format) === $dateString) {
                    return $dt->format('d/m/Y');
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return null;
    }

    public static function fullSql($query)
    {
        $sql = $query->toSql();

        $bindings = $query->getBindings();

        $fullQuery = vsprintf(str_replace(['?'], ['\'%s\''], $sql), $bindings);

        return $fullQuery;
    }

}
