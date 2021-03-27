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

use CoffeePhp\Cache\CacheManager;
use CoffeePhp\Cache\Contract\CacheManagerInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\Factory\CacheFactory;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Integration\CacheComponentRegistrar;
use CoffeePhp\Cache\Test\Fake\FakeBadCacheItemPool;
use CoffeePhp\Cache\Test\Fake\FakeBadCacheItemPool2;
use CoffeePhp\Cache\Test\Fake\FakeBadCacheItemPool3;
use CoffeePhp\Cache\Test\Fake\FakeCacheComponentRegistrar;
use CoffeePhp\Cache\Test\Fake\FakeCacheItemPool;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use CoffeePhp\ComponentRegistry\ComponentRegistry;
use CoffeePhp\Di\Container;
use CoffeePhp\QualityTools\TestCase;
use Psr\Cache\CacheItemPoolInterface;

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
        assertTrue($di->has(CacheFactory::class));
        assertTrue($di->has(CacheFactoryInterface::class));
        assertTrue($di->has(CacheItemFactory::class));
        assertTrue($di->has(CacheItemFactoryInterface::class));

        $register->register(FakeCacheComponentRegistrar::class);

        assertFalse($di->has(CacheItemPoolInterface::class));
        assertFalse($di->has(CacheManager::class));
        assertFalse($di->has(CacheManagerInterface::class));
        assertTrue($di->has(FakeBadCacheItemPool::class));
        assertTrue($di->has(FakeBadCacheItemPool2::class));
        assertTrue($di->has(FakeBadCacheItemPool3::class));
        assertTrue($di->has(FakeCacheItemPool::class));
        assertTrue($di->has(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE));
        assertTrue($di->has(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE_2));
        assertTrue($di->has(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE_3));
        assertTrue($di->has(FakeCacheComponentRegistrar::DI_KEY_FAKE_CACHE));

        assertInstanceOf(CacheKeyValidator::class, $di->get(CacheKeyValidator::class));
        assertSame($di->get(CacheKeyValidator::class), $di->get(CacheKeyValidatorInterface::class));

        assertInstanceOf(CacheFactory::class, $di->get(CacheFactory::class));
        assertSame($di->get(CacheFactory::class), $di->get(CacheFactoryInterface::class));

        assertInstanceOf(CacheItemFactory::class, $di->get(CacheItemFactory::class));
        assertSame($di->get(CacheItemFactory::class), $di->get(CacheItemFactoryInterface::class));

        assertInstanceOf(FakeBadCacheItemPool::class, $di->get(FakeBadCacheItemPool::class));
        assertInstanceOf(FakeBadCacheItemPool2::class, $di->get(FakeBadCacheItemPool2::class));
        assertInstanceOf(FakeBadCacheItemPool3::class, $di->get(FakeBadCacheItemPool3::class));
        assertInstanceOf(FakeCacheItemPool::class, $di->get(FakeCacheItemPool::class));

        $fakeBadCacheManager = $di->get(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE);
        assertInstanceOf(CacheManager::class, $fakeBadCacheManager);
        assertSame($fakeBadCacheManager->getPool(), $di->get(FakeBadCacheItemPool::class));

        $fakeBadCacheManager2 = $di->get(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE_2);
        assertInstanceOf(CacheManager::class, $fakeBadCacheManager2);
        assertSame($fakeBadCacheManager2->getPool(), $di->get(FakeBadCacheItemPool2::class));

        $fakeBadCacheManager3 = $di->get(FakeCacheComponentRegistrar::DI_KEY_FAKE_BAD_CACHE_3);
        assertInstanceOf(CacheManager::class, $fakeBadCacheManager3);
        assertSame($fakeBadCacheManager3->getPool(), $di->get(FakeBadCacheItemPool3::class));

        $fakeCacheManager = $di->get(FakeCacheComponentRegistrar::DI_KEY_FAKE_CACHE);
        assertInstanceOf(CacheManager::class, $fakeCacheManager);
        assertSame($fakeCacheManager->getPool(), $di->get(FakeCacheItemPool::class));

        self::assertException(fn() => $di->get(CacheManager::class));
        self::assertException(fn() => $di->get(CacheManagerInterface::class));
        self::assertException(fn() => $di->get(CacheItemPoolInterface::class));
    }
}
