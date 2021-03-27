<?php

/**
 * FakeCacheItemPool.php
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

/**
 * Class FakeCacheItemPool
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 */
final class FakeCacheItemPool extends AbstractCacheItemPool
{
    private array $fakeCache = [];
    private array $deferred = [];

    /**
     * @inheritDoc
     */
    protected function get(string $key): CacheItemInterface
    {
        return $this->itemFactory->create($key, $this->fakeCache[$key] ?? null, true);
    }

    /**
     * @inheritDoc
     */
    protected function getMultiple(string ...$keys): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key);
        }
    }

    /**
     * @inheritDoc
     */
    protected function has(string $key): bool
    {
        return isset($this->fakeCache[$key]);
    }

    /**
     * @inheritDoc
     */
    protected function deleteAll(): bool
    {
        $this->fakeCache = [];
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function delete(string $key): bool
    {
        unset($this->fakeCache[$key]);
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function deleteMultiple(string ...$keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function set(CacheItemInterface $item): bool
    {
        $this->fakeCache[$item->getKey()] = $item->get();
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function setDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function commitDeferred(): bool
    {
        foreach ($this->deferred as $key => $value) {
            $this->save($value);
        }
        $this->deferred = [];
        return true;
    }
}
