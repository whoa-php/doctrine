<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Whoa\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Whoa\Doctrine\Json\Date;
use Whoa\Doctrine\Traits\DateTypeTrait;

/**
 * @package Whoa\Doctrine
 */
class DateType extends \Doctrine\DBAL\Types\DateType
{
    use DateTypeTrait;

    /** @var string Type name */
    const NAME = 'limoncelloDate';

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $result = null;

        if ($value !== null && ($dateOrNull = parent::convertToPHPValue($value, $platform)) !== null) {
            // despite the name it's not null already
            $result = Date::createFromDateTime($dateOrNull);
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue(
            $this->convertToDateFromString($value, $platform->getDateFormatString(), static::NAME),
            $platform
        );
    }
}
