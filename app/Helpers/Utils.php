<?php

namespace App\Helpers;

class Utils
{
    public static function dd($data)
    {
        dump($data);
        die();
    }
}