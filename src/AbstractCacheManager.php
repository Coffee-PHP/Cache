<?php

/**
 * AbstractCacheManager.php
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
 * @since 2020-10-02
 */

declare(strict_types=1);

namespace CoffeePhp\Cache;

use CoffeePhp\Cache\Contract\CacheManagerInterface;
use CoffeePhp\Cache\Contract\Data\CacheInterface;
use CoffeePhp\Cache\Contract\Data\CacheItemPoolInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;

/**
 * Class AbstractCacheManager
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
abstract class AbstractCacheManager implements CacheManagerInterface
{
    private CacheInterface $cache;
    private CacheItemPoolInterface $pool;

    /**
     * AbstractCacheManager constructor.
     * @param CacheFactoryInterface $cacheFactory
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheFactoryInterface $cacheFactory, CacheItemPoolInterface $pool)
    {
        $this->cache = $cacheFactory->create($pool);
        $this->pool = $pool;
    }

    /**
     * @inheritDoc
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function getPool(): CacheItemPoolInterface
    {
        return $this->pool;
    }
}
