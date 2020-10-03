<?php

/**
 * MockCacheItemPool.php
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

namespace CoffeePhp\Cache\Test\Mock;

use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\AbstractCacheItemPool;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MockCacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 */
final class MockCacheItemPool extends AbstractCacheItemPool
{
    private array $fakeCache = [];
    private array $deferred = [];

    private CacheKeyValidatorInterface $keyValidator;

    /**
     * MockCacheItemPool constructor.
     * @param CacheItemFactoryInterface $itemFactory
     * @param CacheKeyValidatorInterface $keyValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheItemFactoryInterface $itemFactory,
        CacheKeyValidatorInterface $keyValidator,
        LoggerInterface $logger
    ) {
        parent::__construct($itemFactory, $logger);
        $this->keyValidator = $keyValidator;
    }

    /**
     * @inheritDoc
     */
    protected function performGetItem($key): CacheItemInterface
    {
        $this->keyValidator->validate($key);
        return $this->itemFactory->create($key, $this->fakeCache[$key] ?? null, true);
    }

    /**
     * @inheritDoc
     */
    protected function performGetItems(array $keys): iterable
    {
        $keys = $this->keyValidator->validateMultiple($keys);
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->getItem($key);
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    protected function performHasItem($key): bool
    {
        $key = $this->keyValidator->validate($key);
        return isset($this->fakeCache[$key]);
    }

    /**
     * @inheritDoc
     */
    protected function performClear(): bool
    {
        $this->fakeCache = [];
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function performDeleteItem($key): bool
    {
        $this->keyValidator->validate($key);
        unset($this->fakeCache[$key]);
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function performDeleteItems(array $keys): bool
    {
        $this->keyValidator->validateMultiple($keys);
        foreach ($keys as $key) {
            unset($this->fakeCache[$key]);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function performSave(CacheItemInterface $item): bool
    {
        $this->fakeCache[$item->getKey()] = $item->get();
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function performSaveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function performCommit(): bool
    {
        foreach ($this->deferred as $key => $value) {
            $this->save($value);
        }
        $this->deferred = [];
        return true;
    }
}
