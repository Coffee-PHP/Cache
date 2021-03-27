<?php

/**
 * CacheItemFactoryTest.php
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

use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Exception\CacheKeyValidatorException;
use CoffeePhp\Cache\Test\Unit\AbstractCacheTest;
use DateTime;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheItemFactoryTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 * @see CacheItemFactory
 */
final class CacheItemFactoryTest extends AbstractCacheTest
{
    private CacheItemFactory $factory;

    /**
     * @before
     */
    public function setupDependencies(): void
    {
        $this->factory = $this->getClass(CacheItemFactory::class);
    }

    /**
     * @see CacheItemFactory::create()
     */
    public function testCreate(): void
    {
        $cacheItem = $this->factory->create('a', 'b', true, new DateTime());
        assertSame('a', $cacheItem->getKey());
        assertSame('b', $cacheItem->get());
        assertTrue($cacheItem->isHit());
        self::assertException(
            fn() => $this->factory->create('', 'b', true, new DateTime()),
            CacheKeyValidatorException::class,
            'The given key is empty'
        );
        self::assertException(
            fn() => $this->factory->create(2, 'b', true, new DateTime()),
            CacheKeyValidatorException::class,
            'The given key is not a string'
        );
    }
}
