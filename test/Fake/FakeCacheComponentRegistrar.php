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

use CoffeePhp\Cache\Cache;
use CoffeePhp\Cache\CacheItemPool;
use CoffeePhp\Cache\Contract\CacheDriverInterface;
use CoffeePhp\ComponentRegistry\Contract\ComponentRegistrarInterface;
use CoffeePhp\Di\Contract\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class FakeCacheComponentRegistrar
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-26
 */
final class FakeCacheComponentRegistrar implements ComponentRegistrarInterface
{
    /**
     * @var string
     */
    public const FAKE_CACHE = CacheInterface::class . '::' . FakeCacheDriver::class;

    /**
     * @var string
     */
    public const FAKE_CACHE_POOL = CacheItemPoolInterface::class . '::' . FakeCacheDriver::class;

    /**
     * @var string
     */
    public const FAKE_BAD_CACHE = CacheInterface::class . '::' . FakeBadCacheDriver::class;

    /**
     * @var string
     */
    public const FAKE_BAD_CACHE_POOL = CacheItemPoolInterface::class . '::' . FakeBadCacheDriver::class;

    /**
     * @var string
     */
    public const FAKE_BAD_CACHE_2 = CacheInterface::class . '::' . FakeBadCacheDriver2::class;

    /**
     * @var string
     */
    public const FAKE_BAD_CACHE_POOL_2 = CacheItemPoolInterface::class . '::' . FakeBadCacheDriver2::class;

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
        $this->di->bind(FakeCacheDriver::class, FakeCacheDriver::class);
        $this->di->bind(self::FAKE_CACHE, Cache::class, [CacheDriverInterface::class => FakeCacheDriver::class]);
        $this->di->bind(
            self::FAKE_CACHE_POOL,
            CacheItemPool::class,
            [CacheDriverInterface::class => FakeCacheDriver::class]
        );

        $this->di->bind(FakeBadCacheDriver::class, FakeBadCacheDriver::class);
        $this->di->bind(self::FAKE_BAD_CACHE, Cache::class, [CacheDriverInterface::class => FakeBadCacheDriver::class]);
        $this->di->bind(
            self::FAKE_BAD_CACHE_POOL,
            CacheItemPool::class,
            [CacheDriverInterface::class => FakeBadCacheDriver::class]
        );

        $this->di->bind(FakeBadCacheDriver2::class, FakeBadCacheDriver2::class);
        $this->di->bind(
            self::FAKE_BAD_CACHE_2,
            Cache::class,
            [CacheDriverInterface::class => FakeBadCacheDriver2::class]
        );
        $this->di->bind(
            self::FAKE_BAD_CACHE_POOL_2,
            CacheItemPool::class,
            [CacheDriverInterface::class => FakeBadCacheDriver2::class]
        );
    }
}
