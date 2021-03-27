<?php

/**
 * FakeCacheComponentRegistrar.php
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
 * @since 2021-03-26
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Test\Fake;

use CoffeePhp\Cache\CacheManager;
use CoffeePhp\Cache\Contract\CacheManagerInterface;
use CoffeePhp\ComponentRegistry\Contract\ComponentRegistrarInterface;
use CoffeePhp\Di\Contract\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class FakeCacheComponentRegistrar
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-26
 */
final class FakeCacheComponentRegistrar implements ComponentRegistrarInterface
{
    public const DI_KEY_FAKE_BAD_CACHE = CacheManagerInterface::class . '::fake_bad';
    public const DI_KEY_FAKE_BAD_CACHE_2 = CacheManagerInterface::class . '::fake_bad2';
    public const DI_KEY_FAKE_BAD_CACHE_3 = CacheManagerInterface::class . '::fake_bad3';
    public const DI_KEY_FAKE_CACHE = CacheManagerInterface::class . '::fake';

    /**
     * FakeCacheComponentRegistrar constructor.
     * @param ContainerInterface $di
     */
    public function __construct(private ContainerInterface $di)
    {
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->di->bind(FakeBadCacheItemPool::class, FakeBadCacheItemPool::class);
        $this->di->bind(
            self::DI_KEY_FAKE_BAD_CACHE,
            CacheManager::class,
            [CacheItemPoolInterface::class => FakeBadCacheItemPool::class]
        );

        $this->di->bind(FakeBadCacheItemPool2::class, FakeBadCacheItemPool2::class);
        $this->di->bind(
            self::DI_KEY_FAKE_BAD_CACHE_2,
            CacheManager::class,
            [CacheItemPoolInterface::class => FakeBadCacheItemPool2::class]
        );

        $this->di->bind(FakeBadCacheItemPool3::class, FakeBadCacheItemPool3::class);
        $this->di->bind(
            self::DI_KEY_FAKE_BAD_CACHE_3,
            CacheManager::class,
            [CacheItemPoolInterface::class => FakeBadCacheItemPool3::class]
        );

        $this->di->bind(FakeCacheItemPool::class, FakeCacheItemPool::class);
        $this->di->bind(
            self::DI_KEY_FAKE_CACHE,
            CacheManager::class,
            [CacheItemPoolInterface::class => FakeCacheItemPool::class]
        );
    }
}
