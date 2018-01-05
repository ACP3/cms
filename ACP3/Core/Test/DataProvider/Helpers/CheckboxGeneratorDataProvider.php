<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\DataProvider\Helpers;

use ACP3\Core\Test\DataProvider\DataProviderInterface;

class CheckboxGeneratorDataProvider implements DataProviderInterface
{

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'test-checked' => [
                '',
                'bar',
                [
                    [
                        'value' => 'foo',
                        'id' => 'form-field-foo',
                        'checked' => '',
                        'lang' => 'Lorem',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field-bar',
                        'checked' => ' checked="checked"',
                        'lang' => 'Ipsum',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field-baz',
                        'checked' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field'
                    ],
                ]
            ],
            'test-not-checked' => [
                '',
                null,
                [
                    [
                        'value' => 'foo',
                        'id' => 'form-field-foo',
                        'checked' => '',
                        'lang' => 'Lorem',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field-bar',
                        'checked' => '',
                        'lang' => 'Ipsum',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field-baz',
                        'checked' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field'
                    ],
                ]
            ]
        ];
    }
}
