<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Webmozart\Assert\Assert;

trait CanCopyFile
{
    protected function copyFileToPublic(string $source, string $destination): string
    {
        $content = file_get_contents($source);
        Assert::string($content);

        Storage::disk('public')->put($destination, $content);

        return $destination;
    }
}
