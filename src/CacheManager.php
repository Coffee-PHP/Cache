<?php

/**
 * CacheManager.php
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

namespace CoffeePhp\Cache;

use CoffeePhp\Cache\Contract\CacheManagerInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class CacheManager
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-26
 */
final class CacheManager implements CacheManagerInterface
{
    private CacheInterface $cache;

    /**
     * CacheManager constructor.
     * @param CacheFactoryInterface $cacheFactory
     * @param CacheItemPoolInterface $pool
     */
    public function __construct(CacheFactoryInterface $cacheFactory, private CacheItemPoolInterface $pool)
    {
        $this->cache = $cacheFactory->create($pool);
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
