<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper\UpdateCheck;

class UpdateFileParser
{
    /**
     * @return array<string, string>
     *
     * @throws \RuntimeException
     */
    public function parseUpdateFile(string $path): array
    {
        $file = @file_get_contents($path);
        if ($file !== false) {
            [$latestVersion, $url] = explode('||', $file);

            return [
                'latest_version' => $latestVersion,
                'url' => $url,
            ];
        }

        throw new \RuntimeException("Error while fetching the path {$path}.");
    }
}
