<?php

/**
 * CacheItemPoolTest.php
 *
 * Copyright 2021 Danny Damsky
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
 * @since 2021-03-27
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Test\Unit;

use CoffeePhp\Cache\CacheItemPool;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Exception\CacheException;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use CoffeePhp\Cache\Test\Fake\FakeCacheComponentRegistrar;
use Psr\Cache\InvalidArgumentException;
use Throwable;

use function array_keys;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheItemPoolTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 * @see CacheItemPool
 */
final class CacheItemPoolTest extends AbstractCacheTest
{
    private array $fakeCache = [];
    private CacheItemFactoryInterface $factory;
    private CacheItemPool $cache;
    private CacheItemPool $badCache;

    /**
     * @before
     * @throws Throwable|InvalidArgumentException
     */
    public function setupDependencies(): void
    {
        $this->factory = $this->getClass(CacheItemFactoryInterface::class);
        $this->cache = $this->getClass(FakeCacheComponentRegistrar::FAKE_CACHE_POOL);
        $this->badCache = $this->getClass(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_POOL);
        $this->fakeCache = [];
        $this->cache->clear();
        for ($i = 0; $i < 50; ++$i) {
            [$key, $value] = [$this->getFaker()->uuid, $this->getFaker()->paragraph(50)];
            $this->fakeCache[$key] = $value;
            $this->cache->save($this->factory->create($key, $value, true));
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::getItem()
     */
    public function testGetItem(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame($value, $this->cache->getItem($key)->get());
        }
        assertNull($this->cache->getItem('null')->get());
        self::assertException(
            fn() => $this->cache->getItem(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[32]: Failed to fetch a value from cache ; The given key is empty',
            32
        );
        self::assertException(
            fn() => $this->badCache->getItem('test'),
            CacheException::class,
            'CACHESTATE[32]: Failed to fetch a value from cache ; test get',
            32
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::save()
     */
    public function testSave(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame($value, $this->cache->getItem($key)->get());
            $newValue = $this->getFaker()->paragraph(50);
            assertNotSame($value, $newValue);
            assertTrue($this->cache->save($this->factory->create($key, $newValue, true)));
            assertSame($newValue, $this->cache->getItem($key)->get());
        }
        self::assertException(
            fn() => $this->badCache->save($this->factory->create('2', '2', true)),
            CacheException::class,
            'CACHESTATE[64]: Failed to set a value in cache ; test set',
            64
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::deleteItem()
     */
    public function testDeleteItem(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->hasItem($key));
            assertTrue($this->cache->deleteItem($key));
            assertFalse($this->cache->hasItem($key));
        }
        self::assertException(
            fn() => $this->cache->deleteItem(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[128]: Failed to delete a value from cache ; The given key is empty',
            128
        );
        self::assertException(
            fn() => $this->badCache->deleteItem('2'),
            CacheException::class,
            'CACHESTATE[128]: Failed to delete a value from cache ; test delete',
            128
        );
    }

    /**
     * @throws Throwable|InvalidArgumentException
     * @see CacheItemPool::clear()
     */
    public function testClear(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->hasItem($key));
        }
        assertTrue($this->cache->clear());
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->hasItem($key));
        }
        self::assertException(
            fn() => $this->badCache->clear(),
            CacheException::class,
            'CACHESTATE[256]: Failed to clear cache ; test delete all'
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::getItems()
     * @noinspection PhpParamsInspection
     */
    public function testGetItems(): void
    {
        $fromCache = $this->cache->getItems(array_keys($this->fakeCache));
        foreach ($fromCache as $key => $value) {
            assertSame($value->get(), $this->fakeCache[$key]);
        }
        assertNull(iterator_to_array($this->cache->getItems(['null']))['null']->get());
        self::assertException(
            fn() => iterator_to_array($this->cache->getItems(['a', 5, 'b'])),
            CacheInvalidArgumentException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; The given key is not a string',
            33
        );
        self::assertException(
            fn() => iterator_to_array($this->cache->getItems(['a', '', 'b'])),
            CacheInvalidArgumentException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; The given key is empty',
            33
        );
        self::assertException(
            fn() => iterator_to_array($this->badCache->getItems(['a', 'b', 'c'])),
            CacheException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; test get multiple',
            33
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::saveDeferred()
     * @see CacheItemPool::commit()
     */
    public function testTransaction(): void
    {
        $fromCache = $this->cache->getItems(array_keys($this->fakeCache));
        foreach ($fromCache as $key => $value) {
            assertSame($value->get(), $this->fakeCache[$key]);
        }
        $override = $this->getFaker()->paragraph(50);
        foreach ($this->fakeCache as $key => $value) {
            assertTrue($this->cache->saveDeferred($this->factory->create($key, $override, true)));
        }
        assertTrue($this->cache->commit());
        $fromCache = $this->cache->getItems(array_keys($this->fakeCache));
        foreach ($fromCache as $key => $value) {
            assertSame($override, $value->get());
        }
        self::assertException(
            fn() => $this->badCache->saveDeferred($this->factory->create('a', 'b', true)),
            CacheException::class,
            'CACHESTATE[66]: Failed to set a deferred value in cache ; test set deferred',
            66
        );
        self::assertException(
            fn() => $this->badCache->commit(),
            CacheException::class,
            'CACHESTATE[1024]: Failed to commit a cache transaction ; test commit deferred',
            1024
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheItemPool::deleteItems()
     */
    public function testDeleteItems(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->hasItem($key));
        }
        assertTrue($this->cache->deleteItems(array_keys($this->fakeCache)));
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->hasItem($key));
        }
        self::assertException(
            fn() => $this->cache->deleteItems(['a', 5, 'c']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; The given key is not a string',
            129
        );
        self::assertException(
            fn() => $this->cache->deleteItems(['a', '', 'c']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; The given key is empty',
            129
        );
        self::assertException(
            fn() => $this->badCache->deleteItems(['a', 'b', 'c']),
            CacheException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; test delete multiple',
            129
        );
    }

    /**
     * @throws Throwable|InvalidArgumentException
     * @see CacheItemPool::hasItem()
     */
    public function testHasItem(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->hasItem($key));
        }
        assertTrue($this->cache->clear());
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->hasItem($key));
        }
        self::assertException(
            fn() => $this->cache->hasItem(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[512]: Failed to check for the availability of a key in cache ; The given key is empty',
            512
        );
        self::assertException(
            fn() => $this->badCache->hasItem('a'),
            CacheException::class,
            'CACHESTATE[512]: Failed to check for the availability of a key in cache ; test has',
            512
        );
    }
}
