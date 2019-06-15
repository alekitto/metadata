<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Exception\IOException;

trait FileLoaderTrait
{
    private function loadFile(string $filePath): string
    {
        $file_content = @\file_get_contents($filePath);
        if (false === $file_content) {
            $error = \error_get_last();

            throw new IOException(\sprintf(
                "Cannot load file '%s': %s",
                $filePath,
                $error[ 'message' ] ?? 'Unknown error'
            ));
        }

        return $file_content;
    }
}
