<?php

namespace App\Service;

class FileManager
{
    private string $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function prepareFolder(string $destination): void
    {
        $folder = pathinfo($destination, PATHINFO_DIRNAME);

        if (!is_dir($folder) && !mkdir($folder, 0777, true) && !is_dir($folder)) {
            throw new \RuntimeException(sprintf('Folder "%s" was not created', $folder));
        }
    }

    public function save(string $destination, string $data)
    {
        $this->prepareFolder($destination);

        return file_put_contents($destination, $data);
    }

    public function remove(string $path): bool
    {
        return unlink($path);
    }
}
