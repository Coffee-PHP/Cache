<?php

/**
 * CacheComponentRegistrarTest.php
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

namespace CoffeePhp\Cache\Test\Integration;

use CoffeePhp\Cache\Cache;
use CoffeePhp\Cache\CacheItemPool;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Integration\CacheComponentRegistrar;
use CoffeePhp\Cache\Test\Fake\FakeBadCacheDriver;
use CoffeePhp\Cache\Test\Fake\FakeBadCacheDriver2;
use CoffeePhp\Cache\Test\Fake\FakeCacheComponentRegistrar;
use CoffeePhp\Cache\Test\Fake\FakeCacheDriver;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use CoffeePhp\ComponentRegistry\ComponentRegistry;
use CoffeePhp\Di\Container;
use CoffeePhp\QualityTools\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheComponentRegistrarTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheComponentRegistrar
 */
final class CacheComponentRegistrarTest extends TestCase
{
    /**
     * @see CacheComponentRegistrar::register()
     */
    public function testRegister(): void
    {
        $di = new Container();
        $register = new ComponentRegistry($di);
        $register->register(CacheComponentRegistrar::class);

        assertTrue($di->has(CacheKeyValidator::class));
        assertTrue($di->has(CacheKeyValidatorInterface::class));
        assertTrue($di->has(CacheItemFactory::class));
        assertTrue($di->has(CacheItemFactoryInterface::class));

        $register->register(FakeCacheComponentRegistrar::class);

        assertFalse($di->has(CacheItemPoolInterface::class));
        assertFalse($di->has(Cache::class));
        assertFalse($di->has(CacheInterface::class));
        assertTrue($di->has(FakeCacheDriver::class));
        assertTrue($di->has(FakeBadCacheDriver::class));
        assertTrue($di->has(FakeBadCacheDriver2::class));
        assertTrue($di->has(FakeCacheComponentRegistrar::FAKE_BAD_CACHE));
        assertTrue($di->has(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_2));
        assertTrue($di->has(FakeCacheComponentRegistrar::FAKE_CACHE));

        assertInstanceOf(CacheKeyValidator::class, $di->get(CacheKeyValidator::class));
        assertSame($di->get(CacheKeyValidator::class), $di->get(CacheKeyValidatorInterface::class));

        assertInstanceOf(CacheItemFactory::class, $di->get(CacheItemFactory::class));
        assertSame($di->get(CacheItemFactory::class), $di->get(CacheItemFactoryInterface::class));

        assertInstanceOf(FakeCacheDriver::class, $di->get(FakeCacheDriver::class));
        assertInstanceOf(FakeBadCacheDriver::class, $di->get(FakeBadCacheDriver::class));
        assertInstanceOf(FakeBadCacheDriver2::class, $di->get(FakeBadCacheDriver2::class));

        assertInstanceOf(Cache::class, $di->get(FakeCacheComponentRegistrar::FAKE_CACHE));
        assertInstanceOf(CacheItemPool::class, $di->get(FakeCacheComponentRegistrar::FAKE_CACHE_POOL));
        assertInstanceOf(Cache::class, $di->get(FakeCacheComponentRegistrar::FAKE_BAD_CACHE));
        assertInstanceOf(CacheItemPool::class, $di->get(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_POOL));
        assertInstanceOf(Cache::class, $di->get(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_2));
        assertInstanceOf(CacheItemPool::class, $di->get(FakeCacheComponentRegistrar::FAKE_BAD_CACHE_POOL_2));


        self::assertException(fn() => $di->get(Cache::class));
        self::assertException(fn() => $di->get(CacheInterface::class));
        self::assertException(fn() => $di->get(CacheItemPool::class));
        self::assertException(fn() => $di->get(CacheItemPoolInterface::class));
    }
}
