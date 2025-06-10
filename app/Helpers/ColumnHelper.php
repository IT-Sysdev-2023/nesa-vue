<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class ColumnHelper
{
    public static function arrayHelper($title, $column)
    {
        return [
            'title' => $title,
            'dataIndex' => $column,
        ];
    }
}
