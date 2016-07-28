<?php
namespace EdusohoNet\Common;

class Base32 {

    private static $format5 = "%05b";
    private static $format8 = "%08b";

    private static function dict($rev = false)
    {
        $format = self::$format5;
        $dict = array(
            'a' => sprintf($format, 1 - 1),
            'b' => sprintf($format, 2 - 1),
            'c' => sprintf($format, 3 - 1),
            'd' => sprintf($format, 4 - 1),
            'e' => sprintf($format, 5 - 1),
            'f' => sprintf($format, 6 - 1),
            'g' => sprintf($format, 7 - 1),
            'h' => sprintf($format, 8 - 1),
            'i' => sprintf($format, 9 - 1),
            'j' => sprintf($format, 10 - 1),
            'k' => sprintf($format, 11 - 1),
            'l' => sprintf($format, 12 - 1),
            'm' => sprintf($format, 13 - 1),
            'n' => sprintf($format, 14 - 1),
            'o' => sprintf($format, 15 - 1),
            'p' => sprintf($format, 16 - 1),
            'q' => sprintf($format, 17 - 1),
            'r' => sprintf($format, 18 - 1),
            's' => sprintf($format, 19 - 1),
            't' => sprintf($format, 20 - 1),
            'u' => sprintf($format, 21 - 1),
            'v' => sprintf($format, 22 - 1),
            'w' => sprintf($format, 23 - 1),
            'x' => sprintf($format, 24 - 1),
            'y' => sprintf($format, 25 - 1),
            'z' => sprintf($format, 26 - 1),
            '2' => sprintf($format, 27 - 1),
            '3' => sprintf($format, 28 - 1),
            '4' => sprintf($format, 29 - 1),
            '5' => sprintf($format, 30 - 1),
            '6' => sprintf($format, 31 - 1),
            '7' => sprintf($format, 32 - 1)
        );
        if($rev) {
            $rdict = array();
            foreach($dict as $k => $v) $rdict[$v] = $k;
            $dict = $rdict;
        }
        return $dict;
    }

    public static function encode($string)
    {
        $format8 = self::$format8;
        $rdict = self::dict(true);
        $collector = array();
        for($i = 0; $i < strlen($string); $i++)
            $collector[] = sprintf($format8, ord($string[$i]));
        $collector = implode('', $collector);
        $len = strlen($collector);
        if($len % 5) $len += 5 - ($len % 5);
        $collector = str_pad($collector, $len, '0', STR_PAD_RIGHT);
        $string = preg_replace_callback('~[01]{5}~', function($m) use($rdict) {
            return $rdict[$m[0]];
        }, $collector);
        $len = strlen($string);
        if($len % 8) $len += 8 - ($len % 8);
        $string = str_pad($string, $len, '=', STR_PAD_RIGHT);
        return $string;
    }

    public static function safeEncode($string)
    {
        return rtrim(strtr(Self::encode($string), '+/', '-_'), '=');
    }

    public static function decode($string)
    {
        $string = rtrim($string, '=');
        $string = strtolower($string);
        $dict = self::dict();
        $string = preg_replace_callback('~.~', function($m) use($dict) {
            return $dict[$m[0]];
        }, $string);
        $len = strlen($string);
        if($len % 8) $string = substr($string, 0, -($len % 8));
        $string = preg_replace_callback('~[01]{8}~', function($m) {
            return chr(bindec($m[0]));
        }, $string);
        return $string;
    }

    public static function safeDecode($string)
    {
        return self::decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 8, '=', STR_PAD_RIGHT));
    }
}