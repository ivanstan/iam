<?php

namespace App\Service\Generator;

class AbstractGenerator
{
    protected function dump($fileName, $content): void
    {
        if (!file_exists(dirname($fileName)) && !mkdir(dirname($fileName), 0665, true) && !is_dir(dirname($fileName))) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', dirname($fileName)));
        }

        file_put_contents($fileName, $content);
    }
}
