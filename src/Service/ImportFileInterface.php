<?php

namespace App\Service;

interface ImportFileInterface
{
    public function load(string $filePath): self;

    public function insert(): bool;
}
