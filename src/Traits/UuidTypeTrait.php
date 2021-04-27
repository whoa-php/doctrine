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

declare(strict_types=1);

namespace Whoa\Doctrine\Traits;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @package Whoa\Doctrine
 */
trait UuidTypeTrait
{
    /**
     * @param null $value
     *
     * @return UuidInterface
     */
    public function uuid($value = null): UuidInterface
    {
        if ($value instanceof UuidInterface) {
            $uuid = $value;
        } elseif (is_string($value) === true && Uuid::isValid($value) === true) {
            $uuid = $this->parseUuid($value);
        } else {
            $uuid = Uuid::uuid4();
        }

        return $uuid;
    }

    /**
     * @param string $value
     *
     * @return UuidInterface
     */
    private function parseUuid(string $value): UuidInterface
    {
        return Uuid::fromString($value);
    }
}
