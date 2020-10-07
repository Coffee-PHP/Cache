<?php

/**
 * CacheItemTest.php
 *
 * Copyright 2020 Danny Damsky
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Test\Unit\Data;

use CoffeePhp\Cache\Data\CacheItem;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use DateInterval;
use DateTime;
use Faker\Factory;
use Faker\Generator;
use JsonException;
use PHPUnit\Framework\TestCase;

use function json_encode;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheItemTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheItem
 */
final class CacheItemTest extends TestCase
{
    private CacheKeyValidator $cacheKeyValidator;
    private Generator $faker;

    /**
     * CacheItemTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cacheKeyValidator = new CacheKeyValidator();
        $this->faker = Factory::create();
    }

    /**
     * @see CacheItem::get()
     * @see CacheItem::getKey()
     * @see CacheItem::getExpiration()
     * @see CacheItem::isHit()
     */
    public function testGetters(): void
    {
        for ($i = 0; $i < 50; ++$i) {
            $key = $this->faker->uuid;
            $value = $this->faker->paragraph(50);
            $expiration = $this->faker->optional()->dateTimeBetween('now', '+30 years');
            $isHit = true;
            $item = new CacheItem($this->cacheKeyValidator, $key, $value, false, $expiration);
            assertNull($item->get());
            $item->set($value);
            assertSame($key, $item->getKey());
            assertSame($value, $item->get());
            assertSame($isHit, $item->isHit());
            assertSame($expiration, $item->getExpiration());
        }
    }

    /**
     * @see CacheItem::set()
     */
    public function testSet(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $item = new CacheItem($this->cacheKeyValidator, $key, null, true, null);
        assertNull($item->get());
        assertTrue($item->isHit());
        $item->set($value);
        assertSame($value, $item->get());
        assertTrue($item->isHit());
    }

    /**
     * @see CacheItem::expiresAt()
     */
    public function testExpiresAtDateTime(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $item = new CacheItem($this->cacheKeyValidator, $key, $value, true, null);
        $expiration = $this->faker->dateTimeBetween('now', '+30 years');
        assertNull($item->getExpiration());
        $item->expiresAt($expiration);
        assertSame($expiration, $item->getExpiration());
    }

    /**
     * @see CacheItem::expiresAt()
     */
    public function testExpiresAtNull(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $expiration = $this->faker->dateTimeBetween('now', '+30 years');
        $item = new CacheItem($this->cacheKeyValidator, $key, $value, true, $expiration);
        assertSame($expiration, $item->getExpiration());
        $item->expiresAt(null);
        assertNull($item->getExpiration());
    }

    /**
     * @see CacheItem::expiresAfter()
     */
    public function testExpiresAfterInteger(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $expiration = $this->faker->dateTimeBetween('now', '+30 years');
        $item = new CacheItem($this->cacheKeyValidator, $key, $value, true, $expiration);
        assertSame($expiration, $item->getExpiration());
        $item->expiresAfter(300);
        assertSame(
            (new DateTime())->add(new DateInterval('PT300S'))->getTimestamp(),
            $item->getExpiration()->getTimestamp()
        );
    }

    /**
     * @see CacheItem::expiresAfter()
     */
    public function testExpiresAfterNull(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $expiration = $this->faker->dateTimeBetween('now', '+30 years');
        $item = new CacheItem($this->cacheKeyValidator, $key, $value, true, $expiration);
        assertSame($expiration, $item->getExpiration());
        $item->expiresAfter(null);
        assertNull($item->getExpiration());
    }

    /**
     * @see CacheItem::expiresAfter()
     */
    public function testExpiresAfterDateInterval(): void
    {
        $key = $this->faker->uuid;
        $value = $this->faker->paragraph(50);
        $expiration = $this->faker->dateTimeBetween('now', '+30 years');
        $item = new CacheItem($this->cacheKeyValidator, $key, $value, true, $expiration);
        assertSame($expiration, $item->getExpiration());
        $interval = new DateInterval('PT300S');
        $item->expiresAfter($interval);
        assertSame(
            (new DateTime())->add($interval)->getTimestamp(),
            $item->getExpiration()->getTimestamp()
        );
    }

    /**
     * @see CacheItem::serialize()
     */
    public function testSerialization(): void
    {
        $item = new CacheItem(
            $this->cacheKeyValidator,
            'test_key',
            'test value',
            true,
            new DateTime('2020-07-10 12:00:00')
        );
        assertSame(
            'C:30:"CoffeePhp\Cache\Data\CacheItem":109:{a:4:{s:3:"key";s:8:"test_key";s:5:"value";s:10:"test value";s:10:"expiration";i:1594382400;s:6:"is_hit";b:1;}}',
            serialize($item)
        );
    }

    /**
     * @throws JsonException
     * @see CacheItem::jsonSerialize()
     */
    public function testJsonEncoding(): void
    {
        $item = new CacheItem(
            $this->cacheKeyValidator,
            'test_key',
            'test value',
            true,
            new DateTime('2020-07-10 12:00:00')
        );
        assertSame(
            '{"key":"test_key","value":"test value","expiration":1594382400,"is_hit":true}',
            json_encode($item, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @see CacheItem::unserialize()
     */
    public function testUnserialization(): void
    {
        /** @var CacheItem $item */
        $item = unserialize(
            'C:30:"CoffeePhp\Cache\Data\CacheItem":109:{a:4:{s:3:"key";s:8:"test_key";s:5:"value";s:10:"test value";s:10:"expiration";i:1594382400;s:6:"is_hit";b:1;}}'
        );
        assertInstanceOf(CacheItem::class, $item);
        assertSame('test_key', $item->getKey());
        assertSame('test value', $item->get());
        assertSame(1594382400, $item->getExpiration()->getTimestamp());
        assertSame(true, $item->isHit());
    }

    /**
     * @see CacheItem::__toString()
     */
    public function testToString(): void
    {
        $item = new CacheItem(
            $this->cacheKeyValidator,
            'test_key',
            'test value',
            true,
            new DateTime('2020-07-10 12:00:00')
        );
        assertSame(
            'C:30:"CoffeePhp\Cache\Data\CacheItem":109:{a:4:{s:3:"key";s:8:"test_key";s:5:"value";s:10:"test value";s:10:"expiration";i:1594382400;s:6:"is_hit";b:1;}}',
            (string)$item
        );
    }

    /**
     * @see CacheItem::serialize()
     * @see CacheItem::unserialize()
     */
    public function testSerializationAndUnserialization(): void
    {
        for ($i = 0; $i < 50; ++$i) {
            $key = $this->faker->uuid;
            $value = $this->faker->paragraph(50);
            $expiration = $this->faker->optional()->dateTimeBetween('now', '+30 years');
            $isHit = $this->faker->boolean;
            $item = new CacheItem($this->cacheKeyValidator, $key, $value, $isHit, $expiration);
            assertEquals(
                $item,
                unserialize(serialize($item))
            );
        }
    }
}
