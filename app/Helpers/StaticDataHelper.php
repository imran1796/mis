<?php

namespace App\Helpers;

class StaticDataHelper
{
    public static function months(): array
    {
        return [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];
    }

    public static function containerSizes(): array
    {
        return ['dc20', 'dc40', 'dc45', 'r20', 'r40', 'mty20', 'mty40'];
    }

    public static function containerSizesSales(): array
    {
        return ['20ft','40ft','45ft','20R','40R'];
    }
}
