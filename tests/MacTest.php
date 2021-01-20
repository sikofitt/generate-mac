<?php declare(strict_types=1);

/*
 * Copyright (c) 2018  https://sikofitt.com sikofitt@sikofitt.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Sikofitt\GenerateMac\Tests;

use PHPUnit\Framework\TestCase;
use Sikofitt\GenerateMac\Mac;

class MacTest extends TestCase
{
    private const REGEX = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';

    private const NON_UNIQ_PREFIX = '02:bb:01';

    public function testGetMacAddresses(): void
    {
        $mac = new Mac();
        $macAddresses = $mac->getMacAddresses(50);

        $this->assertCount(50, $macAddresses);

        foreach ($macAddresses as $address) {
            $this->assertMatchesRegularExpression(self::REGEX, $address);
        }

        $this->assertMatchesRegularExpression(self::REGEX, $mac->getMacAddress());
        $this->assertSame(Mac::SEPARATOR_COLON, $mac->getSeparator());
        $this->assertTrue($mac->getUnique());
    }

    public function testSeparator(): void
    {
        $mac = new Mac(Mac::SEPARATOR_DASH);

        $this->assertSame(Mac::SEPARATOR_DASH, $mac->getSeparator());
        $this->assertMatchesRegularExpression(self::REGEX, $mac->getMacAddress());
        $mac->setSeparator(Mac::SEPARATOR_COLON);
        $this->assertSame(Mac::SEPARATOR_COLON, $mac->getSeparator());
        $this->assertMatchesRegularExpression(self::REGEX, $mac->getMacAddress());
        $this->assertNotFalse(strpos($mac->getMacAddress(), ':'));
    }

    public function testUnique(): void
    {
        $class = new class extends Mac {
            protected $isTest = true;
        };
        $this->assertTrue($class->getUnique());

        $macAddress = $class->getMacAddress();
        $this->assertStringStartsNotWith(self::NON_UNIQ_PREFIX, $macAddress);
        $class->setUnique(false);
        $this->assertFalse($class->getUnique());
    }

    public function testThrowsOnInvalidPrefix(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $mac = new Mac(4);
    }
}
