<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
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

declare (strict_types=1);

namespace Whoa\Doctrine\Traits;

use DateTimeInterface;
use Doctrine\DBAL\Types\ConversionException;
use Whoa\Doctrine\Json\Time as LimocelloTime;

/**
 * @package Whoa\Doctrine
 */
trait TimeTypeTrait
{
    /**
     * @param        $value
     * @param string $nonJsonFormat
     * @param string $typeName
     * @return DateTimeInterface|null
     * @throws ConversionException
     */
    private function convertToTimeFromString(
        $value,
        string $nonJsonFormat,
        string $typeName
    ): ?DateTimeInterface {
        if ($value instanceof DateTimeInterface || $value === null) {
            $result = $value;
        } elseif (is_string($value) === true) {
            $result = LimocelloTime::createFromFormat($nonJsonFormat, $value);
            $result = $result !== false ?
                $result : LimocelloTime::createFromFormat(LimocelloTime::JSON_API_FORMAT, $value);
            if ($result === false) {
                throw ConversionException::conversionFailed($value, $typeName);
            }
        } else {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                DateTimeInterface::class,
                [DateTimeInterface::class, LimocelloTime::class, 'string']
            );
        }

        return $result;
    }
}
