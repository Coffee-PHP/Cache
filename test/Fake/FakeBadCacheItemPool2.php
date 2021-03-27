<?php

/**
 * FakeBadCacheItemPool2.php
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

namespace CoffeePhp\Cache\Test\Fake;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

/**
 * Class FakeBadCacheItemPool2
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 */
final class FakeBadCacheItemPool2 implements CacheItemPoolInterface
{

    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItemInterface
    {
        throw new RuntimeException('test get item');
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        throw new RuntimeException('test get items');
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        throw new RuntimeException('test has item');
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        throw new RuntimeException('test clear');
    }

    /**
     * @inheritDoc
     */
    public function deleteItem(string $key): bool
    {
        throw new RuntimeException('test delete item');
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        throw new RuntimeException('test delete items');
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test save');
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test save deferred');
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        throw new RuntimeException('test commit');
    }
}
