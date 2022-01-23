<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataProvider\Helpers;

use ACP3\Core\DataProvider\DataProviderInterface;

class ChoicesGeneratorDataProvider implements DataProviderInterface
{
    public function getData(): array
    {
        return [
            'test-not-selected' => [
                '',
                'selected',
                null,
                [
                    [
                        'value' => 'foo',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Lorem',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Ipsum',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field',
                    ],
                ],
            ],
            'test-selected' => [
                '',
                'selected',
                'bar',
                [
                    [
                        'value' => 'foo',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Lorem',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field',
                        'selected' => ' selected="selected"',
                        'lang' => 'Ipsum',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field',
                    ],
                ],
            ],
            'test-checked' => [
                '',
                'checked',
                'bar',
                [
                    [
                        'value' => 'foo',
                        'id' => 'form-field-foo',
                        'checked' => '',
                        'lang' => 'Lorem',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field-bar',
                        'checked' => ' checked="checked"',
                        'lang' => 'Ipsum',
                        'name' => 'form_field',
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field-baz',
                        'checked' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field',
                    ],
                ],
            ],
        ];
    }
}
