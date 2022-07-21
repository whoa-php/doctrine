<?php

/**
 * Copyright 2015-2019 info@neomerx.com
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

declare (strict_types=1);

namespace Whoa\Tests\Doctrine\Types;

use DateTime;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Types\Type;
use Exception;
use Whoa\Doctrine\Json\Date as WhoaDate;
use Whoa\Doctrine\Json\DateTime as WhoaDateTime;
use Whoa\Doctrine\Json\Time as WhoaTime;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Doctrine\Types\DateType;
use Whoa\Doctrine\Types\TimeType;
use Whoa\Tests\Doctrine\TestCase;

/**
 * @package Whoa\Tests\Doctrine
 */
class DateTimeTypesTest extends TestCase
{
    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Type::hasType(DateTimeType::NAME) === true ?: Type::addType(DateTimeType::NAME, DateTimeType::class);
        Type::hasType(DateType::NAME) === true ?: Type::addType(DateType::NAME, DateType::class);
        Type::hasType(TimeType::NAME) === true ?: Type::addType(TimeType::NAME, TimeType::class);
    }

    /**
     * Test date time type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeConversion(): void
    {
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = '2001-02-03T04:05:06+0000';

        $this->assertIsString($dateTime);

        /** @var WhoaDateTime $phpValue */
        $phpValue = $type->convertToPHPValue($dateTime, $platform);

        $this->assertInstanceOf(WhoaDateTime::class, $phpValue);
        $this->assertEquals(981173106, $phpValue->getTimestamp());
        $this->assertEquals($dateTime, $phpValue->jsonSerialize());
    }

    /**
     * Test date time type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeConversion1(): void
    {
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = new DateTime('2001-02-03T04:05:06+0000');

        $this->assertInstanceOf(DateTime::class, $dateTime);

        /** @var WhoaDateTime $phpValue */
        $phpValue = $type->convertToPHPValue($dateTime, $platform);

        $this->assertInstanceOf(WhoaDateTime::class, $phpValue);
        $this->assertEquals(981173106, $phpValue->getTimestamp());
        $this->assertEquals($dateTime->format(WhoaDateTime::JSON_API_FORMAT), $phpValue->jsonSerialize());
    }

    /**
     * Test date time type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeConversion2(): void
    {
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = new WhoaDateTime('2001-02-03T04:05:06+0000');

        $this->assertInstanceOf(WhoaDateTime::class, $dateTime);

        /** @var WhoaDateTime $phpValue */
        $phpValue = $type->convertToPHPValue($dateTime, $platform);

        $this->assertInstanceOf(WhoaDateTime::class, $phpValue);
        $this->assertEquals(981173106, $phpValue->getTimestamp());
        $this->assertEquals($dateTime->format(WhoaDateTime::JSON_API_FORMAT), $phpValue->jsonSerialize());
    }

    /**
     * Test date time type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeToDatabaseConversion1(): void
    {
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = '2001-02-03T04:05:06+0000';

        $this->assertIsString($dateTime);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($dateTime, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('2001-02-03 04:05:06', $databaseValue);
    }

    /**
     * Test date time type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeToDatabaseConversion2(): void
    {
        /** @var DateTimeType $type */
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = new DateTime('2001-02-03 04:05:06');

        $this->assertInstanceOf(DateTime::class, $dateTime);

        /** @var string $phpValue */
        $phpValue = $type->convertToDatabaseValue($dateTime, $platform);
        $this->assertEquals('2001-02-03 04:05:06', $phpValue);
    }

    /**
     * Test date time type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeToDatabaseConversion3(): void
    {
        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = new WhoaDateTime('2001-02-03 04:05:06');

        $this->assertInstanceOf(WhoaDateTime::class, $dateTime);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($dateTime, $platform);

        $this->assertIsString($databaseValue);

        $this->assertEquals('2001-02-03 04:05:06', $databaseValue);
    }

    /**
     * Test date time type to database conversion invalid input.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeToDatabaseConversionInvalidInput1(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = 'XXX';

        $type->convertToDatabaseValue($date, $platform);
    }

    /**
     * Test date time type to database conversion invalid input.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTimeTypeToDatabaseConversionInvalidInput2(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(DateTimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $dateTime = new \stdClass();

        $type->convertToDatabaseValue($dateTime, $platform);
    }

    /**
     * Test date type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeConversion(): void
    {
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = '2001-02-03';

        $this->assertIsString($date);

        /** @var DateTime $phpValue */
        $phpValue = $type->convertToPHPValue($date, $platform);

        $this->assertInstanceOf(WhoaDate::class, $phpValue);

        $this->assertEquals(981158400, $phpValue->getTimestamp());
        $this->assertEquals($date, $phpValue->jsonSerialize());
    }

    /**
     * Test date type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeConversion1(): void
    {
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new DateTime('2001-02-03');

        $this->assertInstanceOf(DateTime::class, $date);

        /** @var DateTime $phpValue */
        $phpValue = $type->convertToPHPValue($date, $platform);

        $this->assertInstanceOf(WhoaDate::class, $phpValue);
        $this->assertEquals(981158400, $phpValue->getTimestamp());
        $this->assertEquals($date->format(WhoaDate::JSON_API_FORMAT), $phpValue->jsonSerialize());
    }

    /**
     * Test date type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeConversion2(): void
    {
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new WhoaDate('2001-02-03');

        $this->assertInstanceOf(WhoaDate::class, $date);

        /** @var DateTime $phpValue */
        $phpValue = $type->convertToPHPValue($date, $platform);

        $this->assertInstanceOf(WhoaDate::class, $phpValue);
        $this->assertEquals(981158400, $phpValue->getTimestamp());
        $this->assertEquals($date->format(WhoaDate::JSON_API_FORMAT), $phpValue->jsonSerialize());
    }

    /**
     * Test date type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeToDatabaseConversion1(): void
    {
        /** @var DateType $type */
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = '2001-02-03';

        $this->assertIsString($date);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($date, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('2001-02-03', $databaseValue);
    }

    /**
     * Test date type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeToDatabaseConversion2(): void
    {
        /** @var DateType $type */
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new DateTime('2001-02-03');

        $this->assertInstanceOf(DateTime::class, $date);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($date, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('2001-02-03', $databaseValue);
    }

    /**
     * Test date type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeToDatabaseConversion3(): void
    {
        /** @var DateType $type */
        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new WhoaDate('2001-02-03');

        $this->assertInstanceOf(WhoaDate::class, $date);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($date, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('2001-02-03', $databaseValue);
    }

    /**
     * Test date type to database conversion invalid type.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeToDatabaseConversionInvalidInput1(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = 'XXX';

        $type->convertToDatabaseValue($date, $platform);
    }

    /**
     * Test date type to database conversion invalid type.
     * @throws Exception
     * @throws DBALException
     */
    public function testDateTypeToDatabaseConversionInvalidInput2(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(DateType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new \stdClass();

        $type->convertToDatabaseValue($date, $platform);
    }

    /**
     * Test time type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeConversion(): void
    {
        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $time = '22:10:01';

        $this->assertIsString($time);

        /** @var DateTime $phpValue */
        $phpValue = $type->convertToPHPValue($time, $platform);

        $this->assertInstanceOf(WhoaTime::class, $phpValue);

        $this->assertEquals(79801, $phpValue->getTimestamp());
        $this->assertEquals($time, $phpValue->jsonSerialize());
    }

    /**
     * Test time type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeToDatabaseConversion1(): void
    {
        /** @var TimeType $type */
        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $time = '22:10:01';

        $this->assertIsString($time);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($time, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('22:10:01', $databaseValue);
    }

    /**
     * Test time type to database conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeToDatabaseConversion2(): void
    {
        /** @var DateType $type */
        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new DateTime('22:10:01');

        $this->assertInstanceOf(DateTime::class, $date);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($date, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('22:10:01', $databaseValue);
    }

    /**
     * Test time type conversion.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeToDatabaseConversion3(): void
    {
        /** @var TimeType $type */
        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new WhoaTime('22:10:01');

        $this->assertInstanceOf(WhoaTime::class, $date);

        /** @var string $databaseValue */
        $databaseValue = $type->convertToDatabaseValue($date, $platform);

        $this->assertIsString($databaseValue);
        $this->assertEquals('22:10:01', $databaseValue);
    }

    /**
     * Test time type to database conversion invalid type.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeToDatabaseConversionInvalidInput1(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = 'XXX';

        $type->convertToDatabaseValue($date, $platform);
    }

    /**
     * Test time type to database conversion invalid type.
     * @throws Exception
     * @throws DBALException
     */
    public function testTimeTypeToDatabaseConversionInvalidInput2(): void
    {
        $this->expectException(\Doctrine\DBAL\Types\ConversionException::class);

        $type = Type::getType(TimeType::NAME);
        $platform = $this->createConnection()->getDatabasePlatform();

        $date = new \stdClass();

        $type->convertToDatabaseValue($date, $platform);
    }

    /**
     * @throws Exception
     */
    public function testDateTimeKeepsTimeZone(): void
    {
        // switch timezone
        $currentTimeZone = date_default_timezone_get();
        date_default_timezone_set('Antarctica/Casey');

        $dateTime = new DateTime('2001-02-03 04:05:06');
        $jsonDateTime = WhoaDateTime::createFromDateTime($dateTime);

        $this->assertEquals('"2001-02-03T04:05:06+0800"', json_encode($jsonDateTime));


        // return timezone back
        date_default_timezone_set($currentTimeZone);
    }
}
