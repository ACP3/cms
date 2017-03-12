<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\DataProvider\Helpers;

use ACP3\Core\Test\DataProvider\DataProviderInterface;

/**
 * Class ChoicesGeneratorDataProvider
 * @package ACP3\Core\Test\DataProvider\Helpers
 */
class ChoicesGeneratorDataProvider implements DataProviderInterface
{

    /**
     * @return array
     */
    public function getData()
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
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Ipsum',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field'
                    ],
                ]
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
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field',
                        'selected' => ' selected="selected"',
                        'lang' => 'Ipsum',
                        'name' => 'form_field'
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field',
                        'selected' => '',
                        'lang' => 'Dolor',
                        'name' => 'form_field'
                    ],
                ]
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
            ]
        ];
    }
}
