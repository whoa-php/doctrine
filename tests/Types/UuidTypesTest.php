<?php

/**
 * Copyright 2021 info@whoaphp.com
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

namespace Whoa\Tests\Doctrine\Types;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Types\Type;
use Whoa\Doctrine\Types\UuidType;
use Whoa\Tests\Doctrine\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @package Whoa\Tests\Doctrine
 */
class UuidTypesTest extends TestCase
{
    /**
     * @inheritDoc
     *
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Type::hasType(UuidType::NAME) === true ?: Type::addType(UuidType::NAME, UuidType::class);
    }

    /**
     * Test UUID type conversion.
     *
     * @throws DBALException
     */
    public function testUuidTypeConversion(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = '0d1e908e-aa29-445b-8f20-c05370edfc41';

        $this->assertIsString($uuid);

        $phpValue = $type->convertToPHPValue($uuid, $platform);

        $this->assertInstanceOf(UuidInterface::class, $phpValue);
        $this->assertEquals($uuid, $phpValue);
    }

    /**
     * Test UUID type conversions.
     *
     * @throws DBALException
     */
    public function testUuidTypeConversion1(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = Uuid::fromString('0d1e908e-aa29-445b-8f20-c05370edfc41');

        $this->assertInstanceOf(UuidInterface::class, $uuid);

        $phpValue = $type->convertToPHPValue($uuid, $platform);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertEquals($uuid, $phpValue);
    }

    /**
     * Test UUID type conversions.
     *
     * @throws DBALException
     */
    public
    function testUuidTypeConversion2(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = Uuid::uuid4();

        $this->assertInstanceOf(UuidInterface::class, $uuid);

        $phpValue = $type->convertToPHPValue($uuid, $platform);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertEquals($uuid, $phpValue);
    }

    /**
     * Test UUID type to database conversion.
     *
     * @throws DBALException
     */
    public function testUuidTypeToDatabaseConversion(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = '0d1e908e-aa29-445b-8f20-c05370edfc41';

        $this->assertIsString($uuid);

        $databaseValue = $type->convertToDatabaseValue($uuid, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals($uuid, $databaseValue);
    }

    /**
     * Test UUID type to database conversion.
     *
     * @throws DBALException
     */
    public function testUuidTypeToDatabaseConversion1(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = Uuid::fromString('0d1e908e-aa29-445b-8f20-c05370edfc41');

        $this->assertInstanceOf(UuidInterface::class, $uuid);

        $databaseValue = $type->convertToDatabaseValue($uuid, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals($uuid, $databaseValue);
    }

    /**
     * Test UUID type to database conversion.
     *
     * @throws DBALException
     */
    public function testUuidTypeToDatabaseConversion2(): void
    {
        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = Uuid::uuid4();

        $this->assertInstanceOf(UuidInterface::class, $uuid);

        $databaseValue = $type->convertToDatabaseValue($uuid, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals($uuid, $databaseValue);
    }

    /**
     * Test UUID type conversion invalid input.
     *
     * @throws DBALException
     */
    public function testUuidTypeConversionInvalidInput(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = 'd`9vX7n[k_>/-*^6';

        $phpValue = $type->convertToPHPValue($uuid, $platform);
    }

    /**
     * Test UUID type conversion invalid input.
     *
     * @throws DBALException.
     */
    public function testUuidTypeConversionInvalidInput1(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = 11;

        $phpValue = $type->convertToPHPValue($uuid, $platform);
    }

    /**
     * Test UUID type conversion invalid input.
     *
     * @throws DBALException.
     */
    public function testUuidTypeConversionInvalidInput2(): void
    {
        $this->expectException(\TypeError::class);

        $type     = Type::getType(UuidType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $uuid = new \stdClass();

        $phpValue = $type->convertToPHPValue($uuid, $platform);
    }
}
