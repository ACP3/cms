<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper\UpdateCheck;

class UpdateFileParser
{
    /**
     * @param string $path
     * @return array
     * @throws \RuntimeException
     */
    public function parseUpdateFile($path)
    {
        $file = @\file_get_contents($path);
        if ($file !== false) {
            list($latestVersion, $url) = \explode('||', $file);

            return [
                'latest_version' => $latestVersion,
                'url' => $url,
            ];
        }

        throw new \RuntimeException("Error while fetching the path {$path}.");
    }
}
