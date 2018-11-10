<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Helpers\StringFormatter;

class Nl2p extends AbstractModifier
{
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;

    /**
     * @param \ACP3\Core\Helpers\StringFormatter $stringFormatter
     */
    public function __construct(StringFormatter $stringFormatter)
    {
        $this->stringFormatter = $stringFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($value): string
    {
        return $this->stringFormatter->nl2p($value);
    }
}
