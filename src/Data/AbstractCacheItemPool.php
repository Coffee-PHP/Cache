<?php

/**
 * AbstractCacheItemPool.php
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
 * @noinspection PhpRedundantCatchClauseInspection
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data;

use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Enum\CacheError;
use CoffeePhp\Cache\Exception\CacheException;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException as Psr6InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as Psr16InvalidArgumentException;
use Throwable;

/**
 * Class AbstractCacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
abstract class AbstractCacheItemPool implements CacheItemPoolInterface
{
    /**
     * AbstractCacheItemPool constructor.
     * @param CacheItemFactoryInterface $itemFactory
     * @param CacheKeyValidatorInterface $keyValidator
     */
    public function __construct(
        protected CacheItemFactoryInterface $itemFactory,
        private CacheKeyValidatorInterface $keyValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    final public function getItem(string $key): CacheItemInterface
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->get($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET(), $e);
        }
    }

    /**
     * Get the cached item for the given key.
     *
     * @param string $key
     *   A string key that has already passed the PSR validation checks.
     *
     * @return CacheItemInterface
     */
    abstract protected function get(string $key): CacheItemInterface;

    /**
     * @inheritDoc
     */
    final public function getItems(array $keys = []): iterable
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            return $this->getMultiple(...$keys);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET_MULTIPLE(), $e);
        }
    }

    /**
     * Get the cached items for the given keys.
     *
     * @param string ...$keys
     *  String keys that have already passed the PSR validation checks.
     *
     * @return iterable<string, CacheItemInterface>
     */
    abstract protected function getMultiple(string ...$keys): iterable;

    /**
     * @inheritDoc
     */
    final public function hasItem(string $key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->has($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::HAS(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::HAS(), $e);
        }
    }

    /**
     * Get whether the given key has a cached value.
     *
     * @param string $key
     *   A string key that has already passed the PSR validation checks.
     *
     * @return bool
     */
    abstract protected function has(string $key): bool;

    /**
     * @inheritDoc
     */
    final public function clear(): bool
    {
        try {
            return $this->deleteAll();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::CLEAR(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::CLEAR(), $e);
        }
    }

    /**
     * Delete all stored cache values.
     *
     * @return bool
     */
    abstract protected function deleteAll(): bool;

    /**
     * @inheritDoc
     */
    final public function deleteItem(string $key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->delete($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE(), $e);
        }
    }

    /**
     * Delete the record with the given key from cache.
     *
     * @param string $key
     *   A string key that has already passed PSR validation checks.
     *
     * @return bool
     */
    abstract protected function delete(string $key): bool;

    /**
     * @inheritDoc
     */
    final public function deleteItems(array $keys): bool
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            return $this->deleteMultiple(...$keys);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE_MULTIPLE(), $e);
        }
    }

    /**
     * Delete the records with the given keys from cache.
     *
     * @param string ...$keys
     *   String keys that have already passed PSR validation checks.
     *
     * @return bool
     */
    abstract protected function deleteMultiple(string ...$keys): bool;

    /**
     * @inheritDoc
     */
    final public function save(CacheItemInterface $item): bool
    {
        try {
            return $this->set($item);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET(), $e);
        }
    }

    /**
     * Store the given item in cache.
     *
     * @param CacheItemInterface $item
     *   The item to store in cache.
     *
     * @return bool
     */
    abstract protected function set(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    final public function saveDeferred(CacheItemInterface $item): bool
    {
        try {
            return $this->setDeferred($item);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET_DEFERRED(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET_DEFERRED(), $e);
        }
    }

    /**
     * Prepare the given item to be stored in cache.
     *
     * @param CacheItemInterface $item
     *   The item to prepare for storage.
     *
     * @return bool
     */
    abstract protected function setDeferred(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    final public function commit(): bool
    {
        try {
            return $this->commitDeferred();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::COMMIT(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::COMMIT(), $e);
        }
    }

    /**
     * Commit the deferred cache items in a single transaction to the cache storage.
     *
     * @return bool
     */
    abstract protected function commitDeferred(): bool;
}
