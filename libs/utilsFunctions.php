<?php

class UtilsFunctions {
    public static function startsWith($string, $startString)
    {   
        $length = strlen($startString);
        return (substr(strtoupper($string), 0, $length) === $startString);
    }

    public static function endsWith($string, $endString)
    {
        $length = strlen($endString);
        if ($length == 0) {
            return true;
        }

        return (substr(strtoupper($string), -$length) === $endString);
    }
}