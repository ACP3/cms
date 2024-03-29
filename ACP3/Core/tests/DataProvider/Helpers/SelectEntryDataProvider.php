<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataProvider\Helpers;

use ACP3\Core\DataProvider\DataProviderInterface;

class SelectEntryDataProvider implements DataProviderInterface
{
    public function getData(): array
    {
        return [
            'not_selected' => [
                'foo',
                1,
                0,
                'selected',
                null,
                '',
            ],
            'value_array_not_selected' => [
                'foo',
                '',
                [
                    'a',
                    'b',
                    'c',
                ],
                'selected',
                null,
                '',
            ],
            'value_array_selected' => [
                'foo',
                'a',
                [
                    'a',
                    'b',
                    'c',
                ],
                'selected',
                null,
                ' selected="selected"',
            ],
            'value_array_post_selected' => [
                'foo',
                'b',
                [
                    'a',
                    'c',
                ],
                'selected',
                [
                    'a',
                    'b',
                    'c',
                ],
                ' selected="selected"',
            ],
            'empty_attribute_selected' => [
                'foo',
                1,
                1,
                '',
                1,
                ' selected="selected"',
            ],
            'checked' => [
                'foo',
                1,
                1,
                'checked',
                1,
                ' checked="checked"',
            ],
        ];
    }
}
