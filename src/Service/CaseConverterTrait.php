<?php

namespace App\Service;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

trait CaseConverterTrait
{
    public function camelCaseToSnakeCase(string $input): string {
        $output = (new CamelCaseToSnakeCaseNameConverter())->normalize($input);

        return str_replace('_', '-', $output);
    }

    public function snakeCaseToCamelCase(string $input, $separator = '-'): string {
        return str_replace($separator, '', ucwords($input, $separator));
    }
}
