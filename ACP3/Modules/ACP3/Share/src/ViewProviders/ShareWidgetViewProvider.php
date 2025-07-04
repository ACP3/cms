<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

class ShareWidgetViewProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(string $path): array
    {
        $path = urldecode($path);

        return [
            'sharing' => [
                'path' => $path,
            ],
        ];
    }
}
