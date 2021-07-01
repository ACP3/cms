<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Services;

interface EmoticonServiceInterface
{
    /**
     * Returns the list of all currently existing emoticons.
     *
     * @return array<string, mixed>
     */
    public function getEmoticonList(): array;
}
