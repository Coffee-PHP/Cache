<?php

/**
 * FakeBadCacheItemPool.php
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

namespace CoffeePhp\Cache\Test\Fake;

use CoffeePhp\Cache\Data\AbstractCacheItemPool;
use Psr\Cache\CacheItemInterface;
use RuntimeException;

/**
 * Class FakeBadCacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 */
final class FakeBadCacheItemPool extends AbstractCacheItemPool
{
    /**
     * @inheritDoc
     */
    protected function get(string $key): CacheItemInterface
    {
        throw new RuntimeException('test get');
    }

    /**
     * @inheritDoc
     */
    protected function getMultiple(string ...$keys): iterable
    {
        throw new RuntimeException('test get multiple');
    }

    /**
     * @inheritDoc
     */
    protected function has(string $key): bool
    {
        throw new RuntimeException('test has');
    }

    /**
     * @inheritDoc
     */
    protected function deleteAll(): bool
    {
        throw new RuntimeException('test delete all');
    }

    /**
     * @inheritDoc
     */
    protected function delete(string $key): bool
    {
        throw new RuntimeException('test delete');
    }

    /**
     * @inheritDoc
     */
    protected function deleteMultiple(string ...$keys): bool
    {
        throw new RuntimeException('test delete multiple');
    }

    /**
     * @inheritDoc
     */
    protected function set(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test set');
    }

    /**
     * @inheritDoc
     */
    protected function setDeferred(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test set deferred');
    }

    /**
     * @inheritDoc
     */
    protected function commitDeferred(): bool
    {
        throw new RuntimeException('test commit deferred');
    }
}
