<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\DateColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\DateTimeColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class UsersModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritDoc}
     */
    protected function getAllowedColumns(): array
    {
        return [
            'super_user' => IntegerColumnType::class,
            'nickname' => TextColumnType::class,
            'pwd' => RawColumnType::class,
            'pwd_salt' => RawColumnType::class,
            'login_errors' => IntegerColumnType::class,
            'realname' => TextColumnType::class,
            'gender' => IntegerColumnType::class,
            'birthday' => DateColumnType::class,
            'birthday_display' => BooleanColumnType::class,
            'mail' => TextColumnType::class,
            'mail_display' => BooleanColumnType::class,
            'website' => TextColumnType::class,
            'icq' => TextColumnType::class,
            'skype' => TextColumnType::class,
            'street' => TextColumnType::class,
            'house_number' => TextColumnType::class,
            'zip' => TextColumnType::class,
            'city' => TextColumnType::class,
            'address_display' => BooleanColumnType::class,
            'country' => TextColumnType::class,
            'country_display' => BooleanColumnType::class,
            'registration_date' => DateTimeColumnType::class,
        ];
    }
}
