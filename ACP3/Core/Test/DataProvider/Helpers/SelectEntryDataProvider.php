<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\DataProvider\Helpers;


use ACP3\Core\Test\DataProvider\DataProviderInterface;

class SelectEntryDataProvider implements DataProviderInterface
{
    /**
     * @return array
     */
    public function getData()
    {
        return [
            'not_selected' => [
                'foo',
                1,
                0,
                'selected',
                null,
                ''
            ],
            'value_array_not_selected' => [
                'foo',
                '',
                [
                    'a',
                    'b',
                    'c'
                ],
                'selected',
                null,
                ''
            ],
            'value_array_selected' => [
                'foo',
                'a',
                [
                    'a',
                    'b',
                    'c'
                ],
                'selected',
                null,
                ' selected="selected"'
            ],
            'value_array_post_selected' => [
                'foo',
                'b',
                [
                    'a',
                    'c'
                ],
                'selected',
                [
                    'a',
                    'b',
                    'c'
                ],
                ' selected="selected"'
            ],
            'empty_attribute_selected' => [
                'foo',
                1,
                1,
                '',
                1,
                ' selected="selected"'
            ],
            'checked' => [
                'foo',
                1,
                1,
                'checked',
                1,
                ' checked="checked"'
            ],
        ];
    }
}
