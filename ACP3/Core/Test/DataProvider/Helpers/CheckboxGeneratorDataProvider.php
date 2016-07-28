<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\DataProvider\Helpers;


use ACP3\Core\Test\DataProvider\DataProviderInterface;

/**
 * Class CheckboxGeneratorDataProvider
 * @package ACP3\Core\Test\DataProvider\Helpers
 */
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
            ],
            'test-not-checked' => [
                '',
                null,
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
                        'checked' => '',
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
