<?php

/**
 * CacheFactory.php
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

namespace CoffeePhp\Cache\Data\Factory;

use CoffeePhp\Cache\Contract\Data\CacheInterface;
use CoffeePhp\Cache\Contract\Data\CacheItemPoolInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Data\Cache;
use Psr\Log\LoggerInterface;

/**
 * Class CacheFactory
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheFactory implements CacheFactoryInterface
{
    private CacheItemFactoryInterface $cacheItemFactory;
    private LoggerInterface $logger;

    /**
     * CacheFactory constructor.
     * @param CacheItemFactoryInterface $cacheItemFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheItemFactoryInterface $cacheItemFactory,
        LoggerInterface $logger
    ) {
        $this->cacheItemFactory = $cacheItemFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function create(CacheItemPoolInterface $cacheItemPool): CacheInterface
    {
        return new Cache(
            $this->cacheItemFactory,
            $cacheItemPool,
            $this->logger
        );
    }
}
