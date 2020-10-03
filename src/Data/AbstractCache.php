<?php

/**
 * AbstractCache.php
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

use CoffeePhp\Cache\Contract\Data\CacheInterface;
use CoffeePhp\Cache\Enum\CacheError;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\InvalidArgumentException as Psr6InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\InvalidArgumentException as Psr16InvalidArgumentException;
use Throwable;

use function is_int;

/**
 * Class AbstractCache
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
abstract class AbstractCache implements CacheInterface
{
    private LoggerInterface $logger;

    /**
     * AbstractCache constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function get($key, $default = null)
    {
        try {
            return $this->performGet($key, $default);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Throwable $e) {
            $this->logException(CacheError::GET(), $e);
            return $default;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::get()}.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     * @throws Psr16InvalidArgumentException
     * @throws Psr6InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performGet($key, $default);

    /**
     * @inheritDoc
     * @param DateInterval|int|null $ttl
     * @psalm-suppress InvalidCatch
     */
    final public function set($key, $value, $ttl = null): bool
    {
        try {
            return $this->performSet($key, $value, $ttl);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::SET(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::set()}.
     *
     * @param mixed $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws Psr16InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performSet($key, $value, $ttl): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    final public function delete($key): bool
    {
        try {
            return $this->performDelete($key);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Throwable $e) {
            $this->logException(CacheError::DELETE(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::delete()}.
     *
     * @param mixed $key
     * @return bool
     * @throws Psr16InvalidArgumentException
     * @throws Psr6InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performDelete($key): bool;

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        try {
            return $this->performClear();
        } catch (Throwable $e) {
            $this->logException(CacheError::CLEAR(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::clear()}.
     *
     * @return bool
     */
    abstract protected function performClear(): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     * @psalm-suppress MixedArgumentTypeCoercion
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getMultiple($keys, $default = null): iterable
    {
        try {
            return $this->performGetMultiple($keys, $default);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Throwable $e) {
            $this->logException(CacheError::GET_MULTIPLE(), $e);
            return array_fill_keys([...$keys], $default);
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::getMultiple()}.
     *
     * @param mixed $keys
     * @param mixed $default
     * @return iterable
     * @throws Psr16InvalidArgumentException
     * @throws Psr6InvalidArgumentException
     * @psalm-return iterable<string, mixed>
     * @phpstan-return iterable<string, mixed>
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performGetMultiple($keys, $default): iterable;

    /**
     * @inheritDoc
     * @param DateInterval|int|null $ttl
     * @psalm-suppress InvalidCatch
     */
    public function setMultiple($values, $ttl = null): bool
    {
        try {
            return $this->performSetMultiple($values, $ttl);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logException(CacheError::SET_MULTIPLE(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::setMultiple()}.
     *
     * @param mixed $values
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws Psr16InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performSetMultiple($values, $ttl): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    public function deleteMultiple($keys): bool
    {
        try {
            return $this->performDeleteMultiple($keys);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Throwable $e) {
            $this->logException(CacheError::DELETE_MULTIPLE(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::deleteMultiple()}.
     *
     * @param mixed $keys
     * @return bool
     * @throws Psr16InvalidArgumentException
     * @throws Psr6InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performDeleteMultiple($keys): bool;

    /**
     * @inheritDoc
     * @psalm-suppress InvalidCatch
     */
    public function has($key): bool
    {
        try {
            return $this->performHas($key);
        } catch (Psr16InvalidArgumentException $e) {
            throw $e;
        } catch (Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        } catch (Throwable $e) {
            $this->logException(CacheError::HAS(), $e);
            return false;
        }
    }

    /**
     * Perform the operation specified in {@see \Psr\SimpleCache\CacheInterface::has()}.
     *
     * @param mixed $key
     * @return bool
     * @throws Psr16InvalidArgumentException
     * @throws Psr6InvalidArgumentException
     * @psalm-suppress InvalidThrow
     */
    abstract protected function performHas($key): bool;

    /**
     * Convert the given TTL parameter to a date time.
     * Return null if the given parameter is null.
     *
     * @param int|DateInterval|null $ttl
     * @return DateTimeInterface|null
     */
    final protected function convertTtlToDateTime($ttl): ?DateTimeInterface
    {
        if (is_int($ttl)) {
            $ttl = new DateInterval("PT{$ttl}S");
        }
        return $ttl !== null ? (new DateTime())->add($ttl) : null;
    }

    /**
     * @param CacheError $cacheError
     * @param Throwable $e
     */
    private function logException(CacheError $cacheError, Throwable $e): void
    {
        $this->logger->error((string)$cacheError, ['exception' => $e]);
    }
}
