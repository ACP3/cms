<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\DataProvider\Helpers;

use ACP3\Core\Test\DataProvider\DataProviderInterface;

/**
 * Class RecordsPerPageDataProvider
 * @package ACP3\Core\Test\DataProvider\Helpers
 */
class RecordsPerPageDataProvider implements DataProviderInterface
{
    /**
     * @return array
     */
    public function getData()
    {
        return [
            'nothing_selected' => [
                '',
                5,
                20,
                null,
                [
                    [
                        'value' => 5,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 5
                    ],
                    [
                        'value' => 10,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 10
                    ],
                    [
                        'value' => 15,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 15
                    ],
                    [
                        'value' => 20,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 20
                    ],
                ]
            ],
            'default_selected' => [
                10,
                5,
                20,
                null,
                [
                    [
                        'value' => 5,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 5
                    ],
                    [
                        'value' => 10,
                        'selected' => ' selected="selected"',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 10
                    ],
                    [
                        'value' => 15,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 15
                    ],
                    [
                        'value' => 20,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 20
                    ],
                ]
            ],
            'post_selected' => [
                '',
                5,
                20,
                15,
                [
                    [
                        'value' => 5,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 5
                    ],
                    [
                        'value' => 10,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 10
                    ],
                    [
                        'value' => 15,
                        'selected' => ' selected="selected"',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 15
                    ],
                    [
                        'value' => 20,
                        'selected' => '',
                        'id' => 'entries',
                        'name' => 'entries',
                        'lang' => 20
                    ],
                ]
            ],
        ];
    }
}
