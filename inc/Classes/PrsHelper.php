<?php

namespace PrioritySync;

class PrsHelper
{
    public static function num_format($num, $after_dot = 0)
    {
        return number_format($num, $after_dot, '.', '');
    }

    public static function debug($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
