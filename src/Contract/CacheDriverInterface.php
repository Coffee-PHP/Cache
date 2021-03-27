<?php

/**
 * CacheDriverInterface.php
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

namespace CoffeePhp\Cache\Contract;

use Psr\Cache\CacheItemInterface;

/**
 * Class CacheDriverInterface
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 */
interface CacheDriverInterface
{
    /**
     * Get the cached item for the given key.
     *
     * @param string $key
     *   A string key that has already passed the PSR validation checks.
     *
     * @return CacheItemInterface
     */
    public function get(string $key): CacheItemInterface;

    /**
     * Get the cached items for the given keys.
     *
     * @param string ...$keys
     *  String keys that have already passed the PSR validation checks.
     *
     * @return iterable<string, CacheItemInterface>
     */
    public function getMultiple(string ...$keys): iterable;

    /**
     * Get whether the given key has a cached value.
     *
     * @param string $key
     *   A string key that has already passed the PSR validation checks.
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Delete the record with the given key from cache.
     *
     * @param string $key
     *   A string key that has already passed PSR validation checks.
     *
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * Delete the records with the given keys from cache.
     *
     * @param string ...$keys
     *   String keys that have already passed PSR validation checks.
     *
     * @return bool
     */
    public function deleteMultiple(string ...$keys): bool;

    /**
     * Delete all stored cache values.
     *
     * @return bool
     */
    public function deleteAll(): bool;

    /**
     * Store the given item in cache.
     *
     * @param CacheItemInterface $item
     *   The item to store in cache.
     *
     * @return bool
     */
    public function set(CacheItemInterface $item): bool;

    /**
     * Prepare the given item to be stored in cache.
     *
     * @param CacheItemInterface $item
     *   The item to prepare for storage.
     *
     * @return bool
     */
    public function setDeferred(CacheItemInterface $item): bool;

    /**
     * Commit the deferred cache items in a single transaction to the cache storage.
     *
     * @return bool
     */
    public function commitDeferred(): bool;
}
