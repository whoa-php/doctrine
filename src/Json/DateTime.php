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

namespace Whoa\Doctrine\Json;

use DateTimeInterface;

/**
 * @package Whoa\Doctrine
 */
class DateTime extends \DateTimeImmutable implements \JsonSerializable
{
    /** @var string Date Time Format */
    const JSON_API_FORMAT = \DateTimeInterface::ISO8601;

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return static
     */
    public static function createFromDateTime(DateTimeInterface $dateTime): self
    {
        $utcTimestamp = $dateTime->getTimestamp();

        // yes, PHP DateTime can accept integer timestamp only as a string ¯\_( ͡° ͜ʖ ͡°)_/¯
        $result = (new self("@$utcTimestamp"))->setTimezone($dateTime->getTimezone());

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->format(static::JSON_API_FORMAT);
    }
}
