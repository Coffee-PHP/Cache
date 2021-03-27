<?php

/**
 * CacheTest.php
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

use CoffeePhp\Cache\Cache;
use CoffeePhp\Cache\Exception\CacheException;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use CoffeePhp\Cache\Test\Fake\FakeCacheComponentRegistrar;
use DateInterval;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Throwable;

use function array_keys;
use function iterator_to_array;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 * @see Cache
 */
final class CacheTest extends AbstractCacheTest
{
    private array $fakeCache = [];
    private Cache $cache;
    private Cache $badCache;
    private Cache $badCache2;

    /**
     * @before
     * @throws Throwable|InvalidArgumentException
     */
    public function setupDependencies(): void
    {
        $this->cache = $this->getClass(FakeCacheComponentRegistrar::FAKE_CACHE);
        $this->badCache = $this->getClass(FakeCacheComponentRegistrar::FAKE_BAD_CACHE);
        $this->badCache2 = $this->getClass(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_2);
        $this->fakeCache = [];
        $this->cache->clear();
        for ($i = 0; $i < 50; ++$i) {
            [$key, $value] = [$this->getFaker()->uuid, $this->getFaker()->paragraph(50)];
            $this->fakeCache[$key] = $value;
            $this->cache->set($key, $value);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::get()
     */
    public function testGet(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame($value, $this->cache->get($key));
        }
        assertNull($this->cache->get('null'));
        self::assertException(
            fn() => $this->cache->get(2),
            CacheInvalidArgumentException::class,
            'CACHESTATE[32]: Failed to fetch a value from cache ; The given key is not a string',
            32
        );
        self::assertException(
            fn() => $this->cache->get(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[32]: Failed to fetch a value from cache ; The given key is empty',
            32
        );
        self::assertException(
            fn() => $this->badCache->get('test'),
            CacheException::class,
            'CACHESTATE[32]: Failed to fetch a value from cache ; test get',
            32
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::set()
     */
    public function testSet(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame($value, $this->cache->get($key));
            $newValue = $this->getFaker()->paragraph(50);
            assertNotSame($value, $newValue);
            assertTrue($this->cache->set($key, $newValue));
            assertSame($newValue, $this->cache->get($key));
        }
        self::assertException(
            fn() => $this->cache->set(2, '2'),
            CacheInvalidArgumentException::class,
            'CACHESTATE[64]: Failed to set a value in cache ; The given key is not a string',
            64
        );
        self::assertException(
            fn() => $this->cache->set('', '2'),
            CacheInvalidArgumentException::class,
            'CACHESTATE[64]: Failed to set a value in cache ; The given key is empty',
            64
        );
        self::assertException(
            fn() => $this->badCache->set('2', '2'),
            CacheException::class,
            'CACHESTATE[64]: Failed to set a value in cache ; test set',
            64
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::delete()
     */
    public function testDelete(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
            assertTrue($this->cache->delete($key));
            assertFalse($this->cache->has($key));
        }
        self::assertException(
            fn() => $this->cache->delete(2),
            CacheInvalidArgumentException::class,
            'CACHESTATE[128]: Failed to delete a value from cache ; The given key is not a string',
            128
        );
        self::assertException(
            fn() => $this->cache->delete(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[128]: Failed to delete a value from cache ; The given key is empty',
            128
        );
        self::assertException(
            fn() => $this->badCache->delete('2'),
            CacheException::class,
            'CACHESTATE[128]: Failed to delete a value from cache ; test delete',
            128
        );
    }

    /**
     * @throws Throwable|InvalidArgumentException
     * @see Cache::clear()
     */
    public function testClear(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
        }
        assertTrue($this->cache->clear());
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->has($key));
        }
        self::assertException(
            fn() => $this->badCache->clear(),
            CacheException::class,
            'CACHESTATE[256]: Failed to clear cache ; test delete all'
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::getMultiple()
     * @noinspection PhpParamsInspection
     */
    public function testGetMultiple(): void
    {
        $fromCache = $this->cache->getMultiple(array_keys($this->fakeCache));
        foreach ($fromCache as $key => $value) {
            assertSame($value, $this->fakeCache[$key]);
        }
        assertSame(['null' => null], iterator_to_array($this->cache->getMultiple(['null'])));
        assertSame(['null' => 'a'], iterator_to_array($this->cache->getMultiple(['null'], 'a')));
        self::assertException(
            fn() => iterator_to_array($this->cache->getMultiple(['a', 5, 'b'])),
            CacheInvalidArgumentException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; The given key is not a string',
            33
        );
        self::assertException(
            fn() => iterator_to_array($this->cache->getMultiple(['a', '', 'b'])),
            CacheInvalidArgumentException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; The given key is empty',
            33
        );
        self::assertException(
            fn() => iterator_to_array($this->badCache->getMultiple(['a', 'b', 'c'])),
            CacheException::class,
            'CACHESTATE[33]: Failed to fetch multiple values from cache ; test get multiple',
            33
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::setMultiple()
     */
    public function testSetMultiple(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame($value, $this->cache->get($key));
        }
        $override = $this->getFaker()->paragraph(50);
        assertTrue($this->cache->setMultiple(array_fill_keys(array_keys($this->fakeCache), $override)));
        assertFalse($this->badCache2->setMultiple(array_fill_keys(array_keys($this->fakeCache), $override)));
        foreach (array_keys($this->fakeCache) as $key) {
            assertSame($override, $this->cache->get($key));
        }
        self::assertException(
            fn() => $this->cache->setMultiple(['a' => 'b', 5 => 'd', 'e' => 'f']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[65]: Failed to set multiple values in cache ; The given key is not a string',
            65
        );
        self::assertException(
            fn() => $this->cache->setMultiple(['a' => 'b', '' => 'd', 'e' => 'f']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[65]: Failed to set multiple values in cache ; The given key is empty',
            65
        );
        self::assertException(
            fn() => $this->badCache->setMultiple(['a' => 'b', 'c' => 'd', 'e' => 'f']),
            CacheException::class,
            'CACHESTATE[65]: Failed to set multiple values in cache ; test set deferred',
            65
        );
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::deleteMultiple()
     */
    public function testDeleteMultiple(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
        }
        assertTrue($this->cache->deleteMultiple(array_keys($this->fakeCache)));
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->has($key));
        }
        self::assertException(
            fn() => $this->cache->deleteMultiple(['a', 5, 'c']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; The given key is not a string',
            129
        );
        self::assertException(
            fn() => $this->cache->deleteMultiple(['a', '', 'c']),
            CacheInvalidArgumentException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; The given key is empty',
            129
        );
        self::assertException(
            fn() => $this->badCache->deleteMultiple(['a', 'b', 'c']),
            CacheException::class,
            'CACHESTATE[129]: Failed to delete multiple values from cache ; test delete multiple',
            129
        );
    }

    /**
     * @throws Throwable|InvalidArgumentException
     * @see Cache::has()
     */
    public function testHas(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
        }
        assertTrue($this->cache->clear());
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->has($key));
        }
        self::assertException(
            fn() => $this->cache->has(2),
            CacheInvalidArgumentException::class,
            'CACHESTATE[512]: Failed to check for the availability of a key in cache ; The given key is not a string',
            512
        );
        self::assertException(
            fn() => $this->cache->has(''),
            CacheInvalidArgumentException::class,
            'CACHESTATE[512]: Failed to check for the availability of a key in cache ; The given key is empty',
            512
        );
        self::assertException(
            fn() => $this->badCache->has('a'),
            CacheException::class,
            'CACHESTATE[512]: Failed to check for the availability of a key in cache ; test has',
            512
        );
    }

    /**
     * @throws ReflectionException
     * @see Cache::convertTtlToDateTime()
     */
    public function testConvertTtlToDateTime(): void
    {
        $reflectionMethod = (new ReflectionClass(Cache::class))->getMethod('convertTtlToDateTime');
        $reflectionMethod->setAccessible(true);

        assertNull($reflectionMethod->invoke($this->cache, null));
        assertGreaterThan(time(), $reflectionMethod->invoke($this->cache, 10)->getTimestamp());
        assertGreaterThan(time(), $reflectionMethod->invoke($this->cache, new DateInterval('PT10S'))->getTimestamp());
    }
}
