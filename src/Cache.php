<?php

/**
 * Cache.php
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
 * @noinspection PhpRedundantCatchClauseInspection
 */

declare(strict_types=1);

namespace CoffeePhp\Cache;

use CoffeePhp\Cache\Contract\CacheDriverInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Enum\CacheError;
use CoffeePhp\Cache\Exception\CacheException;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use DateInterval;
use DateTime;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException as Psr6InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException as Psr16InvalidArgumentException;
use Throwable;

/**
 * Class Cache
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-01
 */
final class Cache implements CacheInterface
{
    /**
     * Cache constructor.
     * @param CacheDriverInterface $driver
     * @param CacheItemFactoryInterface $itemFactory
     * @param CacheKeyValidatorInterface $keyValidator
     */
    public function __construct(
        private CacheDriverInterface $driver,
        private CacheItemFactoryInterface $itemFactory,
        private CacheKeyValidatorInterface $keyValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->get($key)->get() ?? $default;
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            $expiration = $this->convertTtlToDateTime($ttl);
            $item = $this->itemFactory->create($key, $value, true, $expiration);
            return $this->driver->set($item);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->delete($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE(), $e);
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable|Psr16InvalidArgumentException
     */
    public function clear(): bool
    {
        try {
            return $this->driver->deleteAll();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::CLEAR(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::CLEAR(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            $items = $this->driver->getMultiple(...$keys);
            return $this->generateKeyValuePairs($items, $default);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::GET_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::GET_MULTIPLE(), $e);
        }
    }

    /**
     * @param iterable<int, CacheItemInterface> $items
     * @param mixed $default
     * @return iterable
     */
    private function generateKeyValuePairs(iterable $items, mixed $default): iterable
    {
        foreach ($items as $key => $value) {
            yield $key => $value->get() ?? $default;
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        try {
            $expiration = $this->convertTtlToDateTime($ttl);
            foreach ($values as $key => $value) {
                $item = $this->itemFactory->create($key, $value, true, $expiration);
                if (!$this->driver->setDeferred($item)) {
                    return false;
                }
            }
            return $this->driver->commitDeferred();
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::SET_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::SET_MULTIPLE(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        try {
            $keys = $this->keyValidator->validateMultiple($keys);
            return $this->driver->deleteMultiple(...$keys);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::DELETE_MULTIPLE(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::DELETE_MULTIPLE(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        try {
            $key = $this->keyValidator->validate($key);
            return $this->driver->has($key);
        } catch (CacheException $e) {
            throw $e;
        } catch (Psr16InvalidArgumentException | Psr6InvalidArgumentException $e) {
            throw new CacheInvalidArgumentException(CacheError::HAS(), $e);
        } catch (Throwable $e) {
            throw new CacheException(CacheError::HAS(), $e);
        }
    }

    /**
     * Convert the given TTL parameter to a date time.
     * Return null if the given parameter is null.
     *
     * @param DateInterval|int|null $ttl
     * @return DateTime|null
     */
    private function convertTtlToDateTime(mixed $ttl): ?DateTime
    {
        if ($ttl === null) {
            return null;
        }
        if (is_int($ttl)) {
            return (new DateTime())->add(new DateInterval("PT{$ttl}S"));
        }
        return (new DateTime())->add($ttl);
    }
}
