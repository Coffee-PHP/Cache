<?php

/**
 * CacheFactoryTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Data\Factory;

use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Data\Factory\CacheFactory;
use CoffeePhp\Cache\Test\Fake\FakeCacheItemPool;
use CoffeePhp\Cache\Test\Unit\AbstractCacheTest;
use Psr\SimpleCache\InvalidArgumentException;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheFactoryTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 * @see CacheFactory
 */
final class CacheFactoryTest extends AbstractCacheTest
{
    private CacheFactory $factory;
    private FakeCacheItemPool $fakeCacheItemPool;
    private CacheItemFactoryInterface $cacheItemFactory;

    /**
     * @before
     */
    public function setupDependencies(): void
    {
        $this->factory = $this->getClass(CacheFactory::class);
        $this->fakeCacheItemPool = $this->getClass(FakeCacheItemPool::class);
        $this->cacheItemFactory = $this->getClass(CacheItemFactoryInterface::class);
    }

    /**
     * @throws InvalidArgumentException
     * @see CacheFactory::create()
     */
    public function testCreate(): void
    {
        $cache = $this->factory->create($this->fakeCacheItemPool);
        assertFalse($cache->has('test'));
        $this->fakeCacheItemPool->save($this->cacheItemFactory->create('test', 'val', true));
        assertTrue($cache->has('test'));
    }
}
