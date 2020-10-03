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
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data;

use CoffeePhp\Cache\Contract\Data\CacheItemPoolInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Enum\CacheError;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class AbstractCacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
abstract class AbstractCacheItemPool implements CacheItemPoolInterface
{
    protected CacheItemFactoryInterface $itemFactory;
    private LoggerInterface $logger;

    /**
     * AbstractCacheItemPool constructor.
     * @param CacheItemFactoryInterface $itemFactory
     * @param LoggerInterface $logger
     */
    public function __construct(CacheItemFactoryInterface $itemFactory, LoggerInterface $logger)
    {
        $this->itemFactory = $itemFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function getItem($key): CacheItemInterface
    {
        try {
            return $this->performGetItem($key);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::GET(), $e);
            return $this->itemFactory->create($key);
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::getItem()}.
     *
     * @param mixed $key
     * @return CacheItemInterface
     * @throws InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performGetItem($key): CacheItemInterface;

    /**
     * @inheritDoc
     * @return iterable|CacheItemInterface[]
     * @psalm-return iterable<string, CacheItemInterface>
     * @phpstan-return iterable<string, CacheItemInterface>
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidCatch
     */
    final public function getItems(array $keys = []): iterable
    {
        try {
            return $this->performGetItems($keys);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::GET_MULTIPLE(), $e);
            $defaults = [];
            /** @var string $key */
            foreach ($keys as $key) {
                $defaults[$key] = $this->itemFactory->create($key);
            }
            return $defaults;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::getItems()}.
     *
     * @param array $keys
     * @return iterable|CacheItemInterface[]
     * @throws InvalidArgumentException
     * @psalm-return iterable<string, CacheItemInterface>
     * @phpstan-return iterable<string, CacheItemInterface>
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performGetItems(array $keys): iterable;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function hasItem($key): bool
    {
        try {
            return $this->performHasItem($key);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::HAS(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::hasItem()}.
     *
     * @param mixed $key
     * @return bool
     * @throws InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performHasItem($key): bool;

    /**
     * @inheritDoc
     */
    final public function clear(): bool
    {
        try {
            return $this->performClear();
        } catch (Throwable $e) {
            $this->logException(CacheError::CLEAR(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::clear()}.
     *
     * @return bool
     */
    abstract protected function performClear(): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function deleteItem($key): bool
    {
        try {
            return $this->performDeleteItem($key);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::DELETE(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::deleteItem()}.
     *
     * @param mixed $key
     * @return bool
     * @throws InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performDeleteItem($key): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function deleteItems(array $keys): bool
    {
        try {
            return $this->performDeleteItems($keys);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::DELETE_MULTIPLE(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::deleteItems()}.
     *
     * @param array $keys
     * @return bool
     * @throws InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performDeleteItems(array $keys): bool;

    /**
     * @inheritDoc
     */
    final public function save(CacheItemInterface $item): bool
    {
        try {
            return $this->performSave($item);
        } catch (Throwable $e) {
            $this->logException(CacheError::SET(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::save()}.
     *
     * @param CacheItemInterface $item
     * @return bool
     */
    abstract protected function performSave(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    final public function saveDeferred(CacheItemInterface $item): bool
    {
        try {
            return $this->performSaveDeferred($item);
        } catch (Throwable $e) {
            $this->logException(CacheError::SET_DEFERRED(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::saveDeferred()}.
     *
     * @param CacheItemInterface $item
     * @return bool
     */
    abstract protected function performSaveDeferred(CacheItemInterface $item): bool;

    /**
     * @inheritDoc
     */
    final public function commit(): bool
    {
        try {
            return $this->performCommit();
        } catch (Throwable $e) {
            $this->logException(CacheError::COMMIT(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\Cache\CacheItemPoolInterface::commit()}.
     *
     * @return bool
     */
    abstract protected function performCommit(): bool;

    /**
     * @param CacheError $cacheError
     * @param Throwable $e
     */
    private function logException(CacheError $cacheError, Throwable $e): void
    {
        $this->logger->error((string)$cacheError, ['exception' => $e]);
    }
}
