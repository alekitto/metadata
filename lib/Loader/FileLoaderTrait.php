<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Exception\IOException;

use function error_get_last;
use function file_get_contents;
use function sprintf;

trait FileLoaderTrait
{
    private function loadFile(string $filePath): string
    {
        /* @phpstan-ignore-next-line */
        $content = @file_get_contents($filePath);
        if ($content === false) {
            $error = error_get_last();

            throw new IOException(sprintf(
                "Cannot load file '%s': %s",
                $filePath,
                $error['message'] ?? 'Unknown error',
            ));
        }

        return $content;
    }
}
