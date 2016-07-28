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
                        'id' => '',
                        'selected' => '',
                        'lang' => 'Lorem'
                    ],
                    [
                        'value' => 'bar',
                        'id' => '',
                        'selected' => '',
                        'lang' => 'Ipsum'
                    ],
                    [
                        'value' => 'baz',
                        'id' => '',
                        'selected' => '',
                        'lang' => 'Dolor'
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
                        'id' => '',
                        'selected' => '',
                        'lang' => 'Lorem'
                    ],
                    [
                        'value' => 'bar',
                        'id' => '',
                        'selected' => ' selected="selected"',
                        'lang' => 'Ipsum'
                    ],
                    [
                        'value' => 'baz',
                        'id' => '',
                        'selected' => '',
                        'lang' => 'Dolor'
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
                        'lang' => 'Lorem'
                    ],
                    [
                        'value' => 'bar',
                        'id' => 'form-field-bar',
                        'checked' => ' checked="checked"',
                        'lang' => 'Ipsum'
                    ],
                    [
                        'value' => 'baz',
                        'id' => 'form-field-baz',
                        'checked' => '',
                        'lang' => 'Dolor'
                    ],
                ]
            ]
        ];
    }
}
