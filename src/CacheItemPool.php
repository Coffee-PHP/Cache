<?php

/**
 * CacheItemPool.php
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

namespace CoffeePhp\Cache;

use CoffeePhp\Cache\Contract\CacheDriverInterface;
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
 * Class CacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheItemPool implements CacheItemPoolInterface
{
    /**
     * CacheItemPool constructor.
     * @param CacheDriverInterface $driver
     * @param CacheKeyValidatorInterface $keyValidator
     */
    public function __construct(
        private CacheDriverInterface $driver,
        private CacheKeyValidatorInterface $keyValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItemInterface
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->get($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            return $this->driver->getMultiple(...$keys);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET_MULTIPLE(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->has($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::HAS(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::HAS(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        try {
            return $this->driver->deleteAll();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::CLEAR(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::CLEAR(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteItem(string $key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->delete($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            return $this->driver->deleteMultiple(...$keys);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE_MULTIPLE(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        try {
            return $this->driver->set($item);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        try {
            return $this->driver->setDeferred($item);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET_DEFERRED(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET_DEFERRED(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        try {
            return $this->driver->commitDeferred();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException | Psr16InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::COMMIT(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::COMMIT(), $e);
        }
    }
}
