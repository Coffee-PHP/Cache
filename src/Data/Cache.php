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
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data;

use CoffeePhp\Cache\Contract\Data\CacheItemPoolInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use DateInterval;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Cache
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-01
 */
final class Cache extends AbstractCache
{
    private CacheItemFactoryInterface $itemFactory;
    private CacheItemPoolInterface $pool;

    /**
     * Cache constructor.
     * @param CacheItemFactoryInterface $itemFactory
     * @param CacheItemPoolInterface $pool
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheItemFactoryInterface $itemFactory,
        CacheItemPoolInterface $pool,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->itemFactory = $itemFactory;
        $this->pool = $pool;
    }

    /**
     * @inheritDoc
     */
    protected function performGet($key, $default)
    {
        return $this->pool->getItem($key)->get() ?? $default;
    }

    /**
     * @inheritDoc
     */
    protected function performSet($key, $value, $ttl): bool
    {
        $expiration = $this->convertTtlToDateTime($ttl);
        $item = $this->itemFactory->create($key, $value, true, $expiration);
        return $this->pool->save($item);
    }

    /**
     * @inheritDoc
     */
    protected function performDelete($key): bool
    {
        return $this->pool->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    protected function performClear(): bool
    {
        return $this->pool->clear();
    }

    /**
     * @inheritDoc
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    protected function performGetMultiple($keys, $default): iterable
    {
        $items = $this->pool->getItems([...$keys]);
        return $this->generateGetMultipleResults($items, $default);
    }

    /**
     * @param iterable|CacheItemInterface[] $items
     * @param mixed $default
     * @return iterable
     * @psalm-param iterable<string, CacheItemInterface> $items
     * @phpstan-param iterable<string, CacheItemInterface> $items
     * @psalm-return iterable<string, mixed>
     * @phpstan-return iterable<string, mixed>
     */
    private function generateGetMultipleResults(iterable $items, $default): iterable
    {
        foreach ($items as $key => $value) {
            yield $key => $value->get() ?? $default;
        }
    }

    /**
     * @inheritDoc
     */
    protected function performSetMultiple($values, $ttl): bool
    {
        $expiration = $this->convertTtlToDateTime($ttl);
        /**
         * @var mixed $key
         * @var mixed $value
         */
        foreach ($values as $key => $value) {
            $item = $this->itemFactory->create($key, $value, true, $expiration);
            if (!$this->pool->saveDeferred($item)) {
                return false;
            }
        }
        return $this->pool->commit();
    }

    /**
     * @inheritDoc
     */
    protected function performDeleteMultiple($keys): bool
    {
        return $this->pool->deleteItems([...$keys]);
    }

    /**
     * @inheritDoc
     */
    protected function performHas($key): bool
    {
        return $this->pool->hasItem($key);
    }
}
