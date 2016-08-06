<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Exception\IOException;

trait FileLoaderTrait
{
    private function loadFile($filePath)
    {
        $file_content = @file_get_contents($filePath);
        if (false === $file_content) {
            $error = error_get_last();

            throw new IOException(sprintf(
                "Cannot load file '%s': %s",
                $filePath,
                isset($error['message']) ? $error['message'] : 'Unknown error'
            ));
        }

        return $file_content;
    }
}
