<?php

namespace App\Service\Util;

use JetBrains\PhpStorm\Pure;

class ClassUtil
{
    #[Pure] public static function getClassNameFromFqn(string $input): string
    {
        return substr($input, strrpos($input, '\\') + 1);
    }
}
