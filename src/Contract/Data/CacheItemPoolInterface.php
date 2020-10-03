<?php

/**
 * CacheItemPoolInterface.php
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
 * @since 2020-10-01
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Contract\Data;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface as Psr_Cache_CacheItemPoolInterface;

/**
 * Interface CacheItemPoolInterface
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-01
 */
interface CacheItemPoolInterface extends Psr_Cache_CacheItemPoolInterface
{
    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface;

    /**
     * @inheritDoc
     * @return iterable|CacheItemInterface[]
     * @psalm-return iterable<string, CacheItemInterface>
     * @phpstan-return iterable<string, CacheItemInterface>
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function getItems(array $keys = []): iterable;

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool;

    /**
     * @inheritDoc
     */
    public function clear(): bool;

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool;

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool;

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    public function commit(): bool;
}
