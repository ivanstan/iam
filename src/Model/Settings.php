<?php

namespace App\Model;

/**
 *  Array $data contains default settings.
 *
 * @example
 *  [
 *      0 => 'namespace',
 *      1 => 'name',
 *      2 => 'value',
 *  ]
 *
 */
class Settings
{
    public static array $data = [
        ['registration', 'enabled', true],
    ];

    public static function getDefault(string $namespace, string $name)
    {
        foreach (self::$data as $setting) {
            if ($setting[0] === $namespace && $setting[1] === $name) {
                return $setting[2];
            }
        }

        return null;
    }
}
