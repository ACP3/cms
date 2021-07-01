<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Services;

use ACP3\Core\Cache;

class CachingEmoticonService implements EmoticonServiceInterface
{
    private const CACHE_KEY = 'emoticon_list';

    /**
     * @var EmoticonService
     */
    private $emoticonService;
    /**
     * @var Cache
     */
    private $emoticonsCache;

    public function __construct(Cache $emoticonsCache, EmoticonService $emoticonService)
    {
        $this->emoticonsCache = $emoticonsCache;
        $this->emoticonService = $emoticonService;
    }

    public function getEmoticonList(): array
    {
        if (!$this->emoticonsCache->contains(self::CACHE_KEY)) {
            $this->emoticonsCache->save(self::CACHE_KEY, $this->emoticonService->getEmoticonList());
        }

        return $this->emoticonsCache->fetch(self::CACHE_KEY);
    }
}
